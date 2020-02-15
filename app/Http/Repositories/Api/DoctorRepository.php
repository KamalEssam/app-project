<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:57 AM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\DoctorInterface;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\UserTrait;
use App\Models\Account;
use App\Models\Clinic;
use App\Models\Recommendation;
use App\Models\RegisteredDoctors;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use DB;


class DoctorRepository implements DoctorInterface
{
    use UserTrait, DateTrait;

    /**
     * get doctor by unique id
     * @param $unique_id
     * @return mixed
     */
    public static function getDoctorByUniqueId($unique_id)
    {
        return User::where('unique_id', $unique_id)->first();
    }

    /**
     * @param $user_id
     * @return mixed|\stdClass
     */
    public function getActiveDoctor($user_id)
    {
        $account = $this->getActiveDoctorAccount($user_id);

        if (empty($account)) {
            return new \stdClass();
        }

        $doctor = (new AuthRepository())->getUserByAccount($account->id, ApiController::ROLE_DOCTOR);

        if (empty($doctor)) {
            return new \stdClass();
        }
        return $doctor;
    }

    /**
     * get active doctor
     *
     * @param $user_id
     * @return mixed
     */
    public static function getActiveDoctorAccount($user_id)
    {
        return User::join('account_user', 'account_user.user_id', 'users.id')
            ->join('accounts', 'accounts.id', 'account_user.account_id')
            ->where('account_user.user_id', $user_id)
            ->where('account_user.active', ApiController::ACTIVE)
            ->select('accounts.*')
            ->first();
    }

    /**
     * add and remove doctors to user favourite doctors list
     * @param $request
     * @param $user
     * @return mixed
     */
    public function addAndRemoveToFavouriteList($request, $user)
    {
        try {
            // if account not in my list
            if (!$user->myFavouriteDoctors()->where('account_id', $request->account_id)->exists()) {
                $user->myFavouriteDoctors()->attach($request->account_id);

            } else {
                // if in my list remove it
                $user->myFavouriteDoctors()->detach($request->account_id);

            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return new \stdClass();
    }

    /**
     *  get sort by for all Doctors
     *
     * @param $sortType
     * @param $sort_type
     * @return array
     */
    public function getAllDoctorsSortType($sortType, $sort_type)
    {
        switch ($sortType) {
            case 1:
                $sort_type = ['is_premium', 'desc'];
                break;
            case 2:
                $sort_type = ['my_recommends_count', 'desc'];
                break;
            case 3:
                $sort_type = ['followers', 'desc'];
                break;
            default:
                break;
        }
        return $sort_type;
    }

    /**
     *  get the range of prices of Doctors
     *
     * @param $range_from_request
     * @param $price_range
     * @return mixed
     */
    public function getAllDoctorsPriceRange($range_from_request, $price_range)
    {
        foreach ($range_from_request as $item) {
            switch ((int)$item) {
                case 1:
                    $price_range[] = [0, 100];
                    break;
                case 2:
                    $price_range[] = [101, 200];
                    break;
                case 3:
                    $price_range[] = [201, 300];
                    break;
                case 4:
                    $price_range[] = [301];
                    break;
                default:
                    break;
            }
        }
        return $price_range;
    }


    /**
     *
     *  get all doctors
     *
     * @param $request
     * @return mixed
     */
    public function getAllDoctors($request)
    {

        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;

        // get sort By
        $sort_type = ['is_premium', 'desc'];
        if (isset($request->sort_type) && !empty($request->sort_type)) {
            $sort_type = $this->getAllDoctorsSortType($request->sort_type, $sort_type);
        }

        //  get price ranges
        $price_range = [];

        if ($request->price_range != null && is_array($request->price_range)) {
            $price_range = $this->getAllDoctorsPriceRange($request->price_range, $price_range);
        }

        try {
            $doctors = Account::join('users', 'accounts.id', 'users.account_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->leftjoin('specialities', 'specialities.id', 'doctor_details.speciality_id')
                ->leftjoin('account_speciality', 'account_speciality.account_id', 'accounts.id')
                ->leftjoin('clinics', 'clinics.account_id', 'accounts.id')
                ->leftjoin('account_insurance', 'account_insurance.account_id', 'accounts.id')
                ->leftjoin('provinces', 'provinces.id', 'clinics.province_id')
                ->leftjoin('reviews', 'reviews.account_id', 'accounts.id')
                ->where('users.role_id', ApiController::ROLE_DOCTOR)
                ->where('accounts.is_published', ApiController::ACCOUNT_PUBLISHED)
                ->where(function ($query) use ($request) {
                    if (isset($request->city_id) && !empty($request->city_id) && $request->city_id != -1) {
                        $query->where('provinces.city_id', $request->city_id);
                    }
                })->where(function ($query) use ($request) {
                    // search with provinces
                    if (isset($request->province_id) && !empty($request->province_id) && $request->province_id != -1) {
                        $query->where('clinics.province_id', $request->province_id);
                    }
                })->where(function ($query) use ($request) {
                    // search with gender
                    if ($request->has('gender') && in_array($request['gender'], [0, 1])) {
                        $query->where('users.gender', $request['gender']);
                    }
                })->where(function ($query) use ($request) {
                    // filter by speciality
                    if (isset($request->speciality) && !empty($request->speciality)) {
                        $query->where('specialities.id', $request->speciality);
                    }
                })->where(function ($query) use ($request) {
                    // sub specialities
                    if ($request->has('sub_specialities') && is_array($request->sub_specialities) && count($request->sub_specialities) > 0) {
                        $query->whereIn('account_speciality.sub_speciality_id', $request->sub_specialities);
                    }
                })->where(function ($query) use ($request) {
                    // insurance_companies
                    if ($request->has('insurance_companies') && is_array($request->insurance_companies) && count($request->insurance_companies) > 0) {
                        $query->whereIn('account_insurance.insurance_company_id', $request->insurance_companies);
                    }
                })->where(function ($query) use ($price_range) {
                    // set filter for min and max fees
                    if (is_array($price_range) && count($price_range) > 0) {
                        foreach ($price_range as $range) {
                            $query->orWhere('doctor_details.min_fees', '>=', $range[0]);
                            if (isset($range[1])) {
                                $query->where('doctor_details.min_fees', '<=', $range[1]);
                            }
                        }
                    }
                })
                ->select('users.id', 'accounts.type as account_type',
                    'accounts.' . app()->getLocale() . '_name as name',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as image'),
                    'specialities.' . app()->getLocale() . '_speciality as speciality',
                    'doctor_details.min_fees',
                    'doctor_details.min_premium_fees',
                    'users.account_id',
                    'users.unique_id',
                    'doctor_details.featured_rank',
                    'doctor_details.featured_rank as sponsored',
                    'users.is_premium',
                    'accounts.' . app()->getLocale() . '_title as title',
                    DB::raw('IF(reviews.rate IS NULL,0,TRUNCATE(AVG(reviews.rate),2)) as rate')
                )
                ->where(function ($query) use ($request) {
                    // In case of Search
                    if (isset($request['keyword']) && $request['keyword'] != null) {
                        if ($request['keyword'] == 'test-mod-on') {
                            $query->whereIn('users.id', get_test_users('doctor'));
                        } else {
                            $query->where('accounts.en_name', 'like', '%' . $request['keyword'] . '%')
                                ->orWhere('accounts.ar_name', 'like', '%' . $request['keyword'] . '%')
                                ->orWhere('users.name', 'like', '%' . $request['keyword'] . '%');

                        }
                    } else {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->where(function ($query) use ($request) {
                    if (isset($request['keyword']) && $request['keyword'] != null && $request['keyword'] != 'test-mod-on') {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->groupBy('account_id')
                ->withCount('myRecommends')
                ->withCount('reviews as reviews')
                ->withCount('usersWhoFavouriteMe as followers')
                ->orderBy('doctor_details.featured_rank', 'desc')// first order by featured doctors
                ->orderBy($sort_type[0], $sort_type[1])// second by selected sort ( default premium)
                ->orderBy('followers', 'desc')// third by no of followers
                ->orderBy('accounts.created_at', 'desc')
                ->orderBy('users.created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            foreach ($doctors as $doctor) {
                if (($doctor->account_type == ApiController::ACCOUNT_TYPE_POLY)) {
                    $doctor->speciality = trans('lang.different_specialities');
                }
            }

        } catch (\Exception $e) {
            $doctors = new \stdClass();
        }

        $user = auth()->guard('api')->user();   // if the request has authorization token then get the user
        // to add is_recommended and is_favourite
        $doctors = $this->isSetToMyFavouriteDoctors($user, $doctors);
        return $doctors;
    }

    /**
     * get doctor profile details
     * @param $doctor_id
     * @return mixed
     */
    public function getDoctorProfile($doctor_id)
    {
        try {
            $get_doctor_details = Account::join('users', 'accounts.unique_id', 'users.unique_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->leftjoin('account_user', 'accounts.id', 'account_user.account_id')
                ->leftjoin('specialities', 'specialities.id', 'doctor_details.speciality_id')
                ->leftjoin('reviews', 'reviews.account_id', 'accounts.id')
                ->where('users.id', $doctor_id)
                ->where('users.role_id', ApiController::ROLE_DOCTOR)
                ->select('users.unique_id',
                    'accounts.id',
                    'users.id as user_id',
                    'accounts.type as account_type',
                    'accounts.' . app()->getLocale() . '_name as name',
                    'account_user.active',
                    'users.account_id',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as image'),
                    'specialities.' . app()->getLocale() . '_speciality as speciality',
                    'doctor_details.' . app()->getLocale() . '_bio as bio',
                    'doctor_details.min_fees',
                    'doctor_details.min_premium_fees',
                    'accounts.is_published',
                    'users.is_premium',
                    'doctor_details.featured_rank as sponsored',
                    'accounts.' . app()->getLocale() . '_name as account_name',
                    'accounts.' . app()->getLocale() . '_address as account_address',
                    'accounts.' . app()->getLocale() . '_title as title',
                    DB::raw('TRUNCATE(AVG(reviews.rate),2) as rate')
                )
                ->with(array('subSpecialities' => function ($query) {
                    $query->select('sub_specialities.id', 'sub_specialities.' . app()->getLocale() . '_name as name', 'sub_specialities.speciality_id');
                }))
                ->with(array('insuranceCompanies' => function ($query) {
                    $query->select('insurance_companies.id', 'insurance_companies.' . app()->getLocale() . '_name as name');
                }))
                ->with(array('gallery' => function ($query) {
                    $query->select('unique_id', 'image');
                }))
                ->withCount('myRecommends')
                ->withCount('reviews as reviews')
                ->first();

            if (!$get_doctor_details) {
                return false;
            }

            //  check if requested doctor favourite by user or not
            $user = auth()->guard('api')->user();
            if ($user) {
                $checkIfUserFavouriteDoctor = \DB::table('account_user')->where('user_id', $user->id)->where('account_id', $get_doctor_details->id)->first();
                if ($checkIfUserFavouriteDoctor) {
                    $get_doctor_details->is_favourite = 1;
                } else {
                    $get_doctor_details->is_favourite = 0;
                }
            }

            $get_doctor_details->id = $get_doctor_details->user_id;
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        // get doctor services
        $services = Service::join('account_service', 'account_service.service_id', 'services.id')
            ->where('account_id', $get_doctor_details->account_id)
            ->select('account_service.id', 'services.' . app()->getLocale() . '_name as name', 'account_service.price', 'account_service.premium_price')
            ->get();
        $services ? $get_doctor_details->services = $services : $get_doctor_details->services = [];

        // if no speciality
        $get_doctor_details->speciality = ($get_doctor_details->account_type == ApiController::ACCOUNT_TYPE_POLY) ? trans('lang.different_specialities') : $get_doctor_details->speciality;

        if ($get_doctor_details->speciality === null) $get_doctor_details->speciality = " ";

        if ($get_doctor_details->name === null) $get_doctor_details->name = " ";
        // if no address
        if ($get_doctor_details->account_address === null) $get_doctor_details->account_address = " ";
        // if no bio
        if ($get_doctor_details->bio === null) $get_doctor_details->bio = " ";
        // if no active
        if ($get_doctor_details->active === null) $get_doctor_details->active = 0;

        try {
            $doctor_user = self::getUserById($doctor_id);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        try {
            $account = self::getAccountById($doctor_user->account_id);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        $get_doctor_details->views = $account->no_of_views;
        return $get_doctor_details;

    }

    /**
     * add and remove doctors to user favourite doctors list
     * @param $user
     * @param $account_id
     * @return mixed
     */
    public function addDoctorToFavouriteList($user, $account_id)
    {
        if (!$user->myFavouriteDoctors()->where('account_id', $account_id)->exists()) {
            $user->myFavouriteDoctors()->attach($account_id);
        }
    }

    /**
     * get all specialities
     * @return mixed
     */
    public function getSpecialities()
    {
        try {
            $specialities = Speciality::with(
                array('subSpecialities' => function ($query) {
                    $query->select('id', app()->getLocale() . '_name as name', 'speciality_id');
                })
            )
                ->select('specialities.id', 'specialities.image', 'specialities.' . app()->getLocale() . '_speciality as name')
                ->withCount(['offerCategories as categories', 'offers as offers' => function ($query) {
                    $query->whereDate('offers.expiry_date', '>=', now()->format('Y-m-d'));
                }])
                ->get();

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $specialities;
    }


    /**
     * get my doctors list
     * @param $request
     * @param $user
     * @return mixed
     */
    public function getMyDoctorsList($request, $user)
    {
        try {
            $user_doctor_list = Account::join('users', 'accounts.id', 'users.account_id')
                ->join('account_user', 'accounts.id', 'account_user.account_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->leftjoin('specialities', 'specialities.id', 'doctor_details.speciality_id')
                ->where('account_user.user_id', $user->id)
                ->where('users.role_id', ApiController::ROLE_DOCTOR)
                ->select(
                    'accounts.type as account_type',
                    'account_user.active',
                    'users.id',
                    'accounts.type as account_type',
                    'accounts.' . app()->getLocale() . '_name as name',
                    'users.account_id',
                    'users.unique_id',
                    'users.is_premium',
                    'accounts.' . app()->getLocale() . '_title as title',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image),IF((LOCATE("facebook",users.image,1) != 0) OR (LOCATE("google",users.image,1) != 0),users.image,CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image))) as image'),
                    'specialities.' . app()->getLocale() . '_speciality as speciality'
                )
                ->orderBy('account_user.active', 'desc')
                ->get();

            foreach ($user_doctor_list as $doctor) {
                if (($doctor->account_type == ApiController::ACCOUNT_TYPE_POLY)) {
                    $doctor->speciality = trans('lang.different_specialities');
                }
            }

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        try {
            $doctor = $this->isSetToMyFavouriteDoctors($user, $user_doctor_list);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        return $user_doctor_list;
    }

    /**
     * @param $user
     * @param $doctors
     * @param int $is_single_doctor
     * @param null $doctor
     * @return mixed
     */

    public function isSetToMyFavouriteDoctors($user, $doctors, $is_single_doctor = 0, $doctor = null)
    {

        // if not auth then value is set to 0, else check
        $is_auth = ($user != null);
        $is_not_empty = !($doctors instanceof \stdClass);

        try {
            // get the account of all subscribed doctors
            $subscribed_accounts = $user->myFavouriteDoctors;
        } catch (\Exception $e) {
            // in case something went wrong don't mark any subscribed doctors
            $subscribed_accounts = [];
        }
        $min_featured_stars = (new SettingRepository)->getFirstSetting()->min_featured_stars;
        // loop the accounts and add the new field
        if (!$is_single_doctor) {
            foreach ($doctors as $item) {
                $this->addIsRecommendedAndIsFavouriteToDoctor($is_auth, $item, $min_featured_stars, $is_not_empty, $subscribed_accounts);
            }
        } else {
            $this->addIsRecommendedAndIsFavouriteToDoctor($is_auth, $doctor, $min_featured_stars, $is_not_empty, $subscribed_accounts);
        }

        return $doctors;
    }

    /**
     * add image , is_recommended and is_favourite to doctor object
     * @param $is_auth
     * @param $doctor
     * @param $min_featured_stars
     * @param $is_not_empty
     * @param $subscribed_accounts
     * @return mixed
     */
    public function addIsRecommendedAndIsFavouriteToDoctor($is_auth, $doctor, $min_featured_stars, $is_not_empty, $subscribed_accounts)
    {

        if ($doctor->my_recommends_count >= $min_featured_stars) {
            $doctor->is_recommended = 1;
        } else {
            $doctor->is_recommended = 0;
        }

        // in case of authenticated user then mark subscribed Doctors
        if ($is_auth && $is_not_empty && count($subscribed_accounts) > 0) {
            if (in_array($doctor->account_id, $subscribed_accounts->pluck('id')->toArray())) {
                $doctor->active = $subscribed_accounts->where('id', $doctor->account_id)->pluck('pivot')->pluck('active')->first();
                $doctor->is_favourite = 1;
            } else {
                $doctor->is_favourite = 0;
                $doctor->active = 0;
            }
        }
    }

    /**
     * get all doctor clinics
     * @param $doctor_id
     * @return mixed
     */
    public function getDoctorClinics($doctor_id)
    {
        $doctor_user = self::getUserById($doctor_id);
        if (!$doctor_user) {
            return false;
        }
        $doctor_account = self::getAccountById($doctor_user->account_id);
        if (!$doctor_account) {
            return false;
        }
        try {
            if ($doctor_account->type == ApiController::ACCOUNT_TYPE_POLY) {
                $get_doctor_clinics = Clinic::join('users', 'clinics.account_id', 'users.account_id')
                    ->join('accounts', 'clinics.account_id', 'accounts.id')
                    ->join('specialities', 'clinics.speciality_id', 'specialities.id')
                    ->where('accounts.type', ApiController::ACCOUNT_TYPE_POLY)
                    ->where('users.id', $doctor_id)
                    ->where('users.role_id', ApiController::ROLE_DOCTOR)
                    ->select('clinics.id',
                        'clinics.fees',
                        'clinics.premium_fees',
                        'accounts.type as account_type',
                        'specialities.' . app()->getLocale() . '_speciality as speciality',
                        'clinics.' . app()->getLocale() . '_name',
                        DB::raw('CONCAT(clinics.' . app()->getLocale() . '_name," ", "(" ," ",specialities.' . app()->getLocale() . '_speciality," ", ")") AS name'),
                        'clinics.pattern'
                    )
                    ->get()->reject(function ($value, $key) {
                        // remove the days with no working hours
                        $clinic = json_decode($value);
                        return ($clinic->working_hours_start_end) == '';
                    })->values();
            } else {

                $get_doctor_clinics = Clinic::join('users', 'clinics.account_id', 'users.account_id')
                    ->join('provinces', 'provinces.id', 'clinics.province_id')
                    ->join('accounts', 'accounts.id', 'clinics.account_id')
                    ->join('doctor_details', 'doctor_details.account_id', 'accounts.id')
                    ->join('specialities', 'doctor_details.speciality_id', 'specialities.id')
                    ->where('accounts.type', ApiController::ACCOUNT_TYPE_SINGLE)
                    ->where('users.id', $doctor_id)
                    ->where('users.role_id', ApiController::ROLE_DOCTOR)
                    ->select('clinics.id',
                        'clinics.fees',
                        'clinics.premium_fees',
                        'clinics.lat',
                        'clinics.lng',
                        'accounts.type as account_type', 'specialities.' . app()->getLocale() . '_speciality as speciality',
                        'clinics.pattern',
                        'provinces.' . app()->getLocale() . '_name as province_name',
                        DB::raw('clinics.' . app()->getLocale() . '_address as name')
                    )
                    ->get()->reject(function ($value, $key) {
                        // remove the days with no working hours
                        $clinic = json_decode($value);
                        if (($clinic->working_hours_start_end) == '') {
                            return true;
                        }
                        return false;
                    })->values();
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $get_doctor_clinics;
    }

    /**
     *  deactivate the current doctor and activate the given doctor
     *
     * @param $auth
     * @param $account_id
     * @return mixed
     */

    public function deactivateCurrentDoctorAndActivateTheGivenDoctor($auth, $account_id)
    {
        try {
            //  get current active doctor
            $getCurrentActiveDoctor = DB::table('account_user')->where('user_id', $auth->id)
                ->where('active', ApiController::ACTIVE)->first();

            if ($getCurrentActiveDoctor) {
                // if the activated doctor is the given doctor
                if ($getCurrentActiveDoctor->account_id == $account_id) {
                    $doctor_user_model = (new AuthRepository())->getUserByAccount($account_id, ApiController::ROLE_DOCTOR);
                    return $this->getDoctorProfile($doctor_user_model->id);
                }
                // update current to be inactive
                DB::table('account_user')->where('user_id', $auth->id)
                    ->where('active', ApiController::ACTIVE)
                    ->update(array('active' => ApiController::ACTIVE));

                $active_doctor = RegisteredDoctors::firstOrNew(array(
                    'user_id' => $auth->id,
                    'active' => 1,
                ));

                $active_doctor->active = 0;
                $active_doctor->save();
            }
            // activate the given doctor
            try {
                $active_doctor = RegisteredDoctors::firstOrNew(array(
                    'account_id' => $account_id,
                    'user_id' => $auth->id
                ));
                $active_doctor->active = 1;
                $active_doctor->save();
                $doctor_user_model = (new AuthRepository())->getUserByAccount($account_id, ApiController::ROLE_DOCTOR);
                return $this->getDoctorProfile($doctor_user_model->id);
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

    }


    /**
     * get doctor by account id
     * @param $account_id
     * @return mixed
     */
    public static function getDoctorByAccountId($account_id)
    {
        try {
            return User::where('account_id', $account_id)->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

    }

    /**
     * check if this doctor is published = 1 that mean doctor have clinics and due date > today
     * @param $account_id
     * @return mixed
     */
    public function CheckIfDoctorPublished($account_id)
    {
        return Account::where('id', $account_id)
            ->where('is_published', ApiController::ACCOUNT_PUBLISHED)
            ->first();

    }

    /**
     *increase views when patient view doctor account
     * @param $account_id
     * @return mixed
     * @throws \Exception
     */
    public function counterViewsForAccount($account_id)
    {
        try {
            $account = self::getAccountById($account_id);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::beginTransaction();
        try {
            $account->no_of_views += 1;
            $account->update();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::commit();
    }

    /**
     *
     *  get all doctors for the website
     *
     * @param $request
     * @return mixed
     */
    public function SiteAllDoctors($request)
    {
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;
        $order_by = ['is_premium', 'desc'];
        if (isset($request['sort']) && in_array($request['sort'], [0, 1, 2, 3])) {
            switch ($request['sort']) {
                case '0' :
                    $order_by = ['followers', 'desc'];
                    break;
                case '1':
                    $order_by = ['my_recommends_count', 'desc'];
                    break;
                case '2':
                    $order_by = ['account_name', 'asc'];
                    break;
                default:
                    $order_by = ['is_premium', 'desc'];
            }
        }

        try {
            $min_featured_stars = (new SettingRepository)->getFirstSetting()->min_featured_stars;
            $doctors = Account::join('users', 'accounts.id', 'users.account_id')
                ->join('doctor_details', 'accounts.id', 'doctor_details.account_id')
                ->leftjoin('specialities', 'specialities.id', 'doctor_details.speciality_id')
                ->leftjoin('clinics', 'clinics.account_id', 'accounts.id')
                ->leftjoin('provinces', 'provinces.id', 'clinics.province_id')
                ->leftjoin('account_speciality', 'account_speciality.account_id', 'accounts.id')
                ->leftjoin('account_insurance', 'account_insurance.account_id', 'accounts.id')
                ->leftjoin('reviews', 'reviews.account_id', 'accounts.id')
                ->where('users.role_id', ApiController::ROLE_DOCTOR)
                ->where('accounts.is_published', ApiController::ACCOUNT_PUBLISHED)
                ->where(function ($query) use ($request) {
                    if (isset($request['slug']) && !is_null($request['slug'])) {
                        if ($request['slug'] == '0') {
                            $query->where('accounts.type', 1);
                        } else {
                            $query->where('specialities.slug', $request['slug']);
                        }
                    }
                })
                ->where(function ($query) use ($request) {
                    if (isset($request->city_id) && !empty($request->city_id) && $request->city_id != -1) {
                        $query->where('provinces.city_id', $request->city_id);
                    }

                })
                ->where(function ($query) use ($request) {
                    // search with provinces
                    if (isset($request->province_id) && !empty($request->province_id) && $request->province_id != -1) {
                        $query->where('clinics.province_id', $request->province_id);
                    }
                })
                ->where(function ($query) use ($request) {
                    // search with gender
                    if ($request->has('gender') && in_array($request['gender'], [0, 1])) {
                        $query->where('users.gender', $request['gender']);
                    }
                })
                ->where(function ($query) use ($request) {
                    // filter by speciality
                    if (isset($request->speciality) && !empty($request->speciality)) {
                        $query->where('specialities.id', $request->speciality);
                    }

                })
                ->where(function ($query) use ($request) {
                    // insurance_companies
                    if ($request->has('insurance_companies') && is_array($request->insurance_companies) && count($request->insurance_companies) > 0) {
                        $query->whereIn('account_insurance.insurance_company_id', $request->insurance_companies);
                    }

                })
                ->select(
                    'users.id',
                    'users.account_id',
                    'users.name',
                    'users.unique_id'
                    , 'accounts.' . app()->getLocale() . '_title as title'
                    , DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image),IF((LOCATE("facebook",users.image,1) != 0) OR (LOCATE("google",users.image,1) != 0),users.image,CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image))) as image')
                    , 'specialities.' . app()->getLocale() . '_speciality as speciality',
                    'doctor_details.min_fees',
                    'doctor_details.min_premium_fees',
                    'doctor_details.' . app()->getLocale() . '_bio as bio',
                    'accounts.' . app()->getLocale() . '_name as account_name',
                    'accounts.no_of_views',
                    'users.is_premium',
                    'users.created_at',
                    'doctor_details.featured_rank',
                    'doctor_details.featured_rank as sponsored',
                    DB::raw('IF(reviews.rate IS NULL,0,TRUNCATE(AVG(reviews.rate),2)) as rate')
                )
                ->where(function ($query) use ($request) {
                    // In case of Search
                    if (isset($request['keyword']) && $request['keyword'] != null) {
                        if ($request['keyword'] == 'test-mod-on') {
                            $query->whereIn('users.id', get_test_users('doctor'));
                        } else {
                            $query->where('accounts.en_name', 'like', '%' . $request['keyword'] . '%')
                                ->orWhere('accounts.ar_name', 'like', '%' . $request['keyword'] . '%')
                                ->orWhere('users.name', 'like', '%' . $request['keyword'] . '%');

                        }
                    } else {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->where(function ($query) use ($request) {
                    if (isset($request['keyword']) && $request['keyword'] != null && $request['keyword'] != 'test-mod-on') {
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->groupBy('users.account_id')
                ->withCount('myRecommends')
                ->withCount('usersWhoFavouriteMe as followers')
                ->withCount('reviews as reviews')
                ->orderBy('doctor_details.featured_rank', 'desc')// first order by featured doctors
                ->orderBy($order_by[0], $order_by[1])// second by selected sort ( default premium)
                ->orderBy('followers', 'desc')// third by no of followers
                ->orderBy('accounts.created_at', 'desc')
                ->orderBy('users.created_at', 'desc')
                ->limit($limit)
                ->paginate($limit);


            foreach ($doctors as $doctor) {
                if ($doctor->my_recommends_count >= $min_featured_stars) {
                    $doctor->is_recommended = 1;
                } else {
                    $doctor->is_recommended = 0;
                }
            }

        } catch (\Exception $e) {
            $doctors = new \stdClass();
        }

        return $doctors;
    }

    /**
     * recommend this doctor
     * @param $user_id
     * @param $account_id
     * @param $receiver_serial
     * @return mixed
     */
    public
    function recommendDoctorAccount($user_id, $account_id, $receiver_serial)
    {
        try {
            $recommend_doctor = Recommendation::create([
                'user_id' => $user_id,
                'account_id' => $account_id,
                'receiver_serial' => $receiver_serial
            ]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $user_id
     * @param $account_id
     * @param $serial
     * @return bool|mixed
     */
    public
    function checkIfRecommendationExists($user_id, $account_id, $serial)
    {
        try {
            $recommendation = (new Recommendation())->where('user_id', $user_id)
                ->where('account_id', $account_id)
                ->where('receiver_serial', $serial)
                ->first();
            if (!$recommendation) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * get doctor by account id
     * @param $account_id
     * @return mixed
     */
    public static function getDoctorInfoForReviewByAccountId($account_id)
    {
        try {
            return User::join('accounts', 'accounts.id', 'users.account_id')
                ->where('accounts.id', $account_id)
                ->select('users.id', 'users.is_premium', 'users.unique_id', 'accounts.' . app()->getLocale() . '_name as name', 'users.image')
                ->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

    }
}
