<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\DoctorRepository;
use App\Http\Traits\UserTrait;
use App\Models\Account;
use DB;
use Illuminate\Http\Request;

class DoctorController extends ApiController
{
    private $doctorRepository;
    use UserTrait;

    /**
     * DoctorController constructor.
     * @param Request $request
     * @param DoctorRepository $doctorRepository
     */
    public function __construct(Request $request, DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->setLang($request);
    }

    /**
     *  get list of doctors for the website
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteDoctorsList(Request $request)
    {
        // set language
        $this->setLang($request);
        $doctors = $this->doctorRepository->SiteAllDoctors($request);
        return self::jsonResponse(true, self::CODE_OK, trans('lang.all-doctors'), new \stdClass(), $doctors);
    }


    /**
     * @param $request
     * @return bool
     */
    public function getMyDoctorsList(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if nit authorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;

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
                    'accounts.' . app()->getLocale() . '_name as account_name',
                    'users.account_id',
                    'users.unique_id',
                    'users.is_premium',
                    'doctor_details.min_fees',
                    'accounts.no_of_views',
                    'accounts.' . app()->getLocale() . '_title as title',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image),IF((LOCATE("facebook",users.image,1) != 0) OR (LOCATE("google",users.image,1) != 0),users.image,CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image))) as image'),
                    'specialities.' . app()->getLocale() . '_speciality as speciality'
                )
                ->withCount('usersWhoFavouriteMe as followers')
                ->orderBy('account_user.active', 'desc')
                ->paginate($limit);

            foreach ($user_doctor_list as $doctor) {
                if (($doctor->account_type == ApiController::ACCOUNT_TYPE_POLY)) {
                    $doctor->speciality = trans('lang.different_specialities');
                }
            }

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.all-doctors'), new \stdClass(), $user_doctor_list);

    }

}
