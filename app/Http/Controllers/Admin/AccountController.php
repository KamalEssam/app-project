<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserGenerated;
use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AccountRepository;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\CountryRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\PlanRepository;
use App\Http\Repositories\Web\SettingRepository;
use App\Http\Repositories\Web\SpecialityRepository;
use App\Http\Repositories\Web\WorkingHourRepository;
use App\Http\Traits\MailTrait;
use App\Models\Account;
use App\Models\AccountService;
use App\Models\City;
use App\Models\DoctorDetail;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Event;
use DB;
use App\Http\Requests\AccountRequest;
use Illuminate\Http\Request;

class AccountController extends WebController
{
    use MailTrait;

    protected $accountRepository, $publish_errors;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Display a listing of all account in the application
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('type')) {
            $type = $request->type;
        } else {
            $type = -1;
        }
        $accounts = $this->accountRepository->getAllAccountsOrdered($type);

        return view('admin.rk-admin.accounts.index', compact('accounts'));
    }

    /**
     *  show create new account form and get the necessary options
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $plans = (new PlanRepository())->getAllPlans();

        if (!$plans) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_plans'));
        }
        $countries = (new CountryRepository())->getAllCountries();
        if (!$countries) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_countries'));
        }
        $cities = (new CityRepository())->getAllCities();
        if (!$cities) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_cities'));
        }
        return view('admin.rk-admin.accounts.create', compact('plans', 'countries', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(AccountRequest $request)
    {
        // find logged in User
        $auth_user = auth()->user();

        DB::beginTransaction();
        //create user for the account
        try {
            // create new user
            $user = (new AuthRepository())->createUSer($request, self::ACTIVE);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'), 'accounts.index');
        }

        // generate a new unique id for this user and update all counters
        try {
            Event::fire(new UserGenerated(self::ROLE_DOCTOR, $user));
        } catch (\Exception $e) {
            DB::rollBack();

            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.unique_id_error'), 'accounts.index');
        }

        // get application settings
        $setting = SettingRepository::getFirstSetting();
        if (!$setting) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_settings'), 'accounts.index');
        }

        //create account after user to fire event first to get account counter
        try {
            $account = (new AccountRepository())->createAccount($request);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_add_err'), 'accounts.index');
        }

        // at first get any speciality and assign it to the user, the user can change it later
        $speciality = SpecialityRepository::getFirstSpeciality();
        if (!$speciality) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_specialities'), 'accounts.index');
        }

        // create doctor details table
        try {
            (new DoctorDetailsRepository())->createDoctorDetail($account->id, $speciality->id);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.doctor_details_add_err'), 'accounts.index');
        }

        $account->created_by = $auth_user->id;
        $account->unique_id = 'RK_ACC_' . (999 + $setting->account_counter);
        $account->save();

        //update user data
        $user->unique_id = $account->unique_id;
        $user->account_id = $account->id;
        $user->save();

        // get the plan by id
        $plan = (new PlanRepository())->getPlanById($account->plan_id);
        if (!$plan) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'), 'accounts.index');
        }

        // calculate the Due amount for the account plan
        $account->due_amount = $request->days * $plan->price_of_day;
        // calculate the Due data for the account plan
        $account->due_date = Carbon::today()->addDays(4);
        $account->save();

        // if password of user not found send message to set password
        if ($user->password == NULL) {
            // include try and catch in sending email in case something went wrong
            try {
                $data = [
                    'user' => $user,
                    'subject' => 'Set your account password',
                    'view' => 'emails.setPassword',
                    'to' => $user->email,
                ];
                $this->sendMailTraitFun($data);
            } catch (\Exception $e) {
                DB::rollBack();
                self::logErr($e->getMessage());
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.set_password_email_err'), 'accounts.index');
            }
        }
        // in case all is ok
        DB::commit();
        // account added successfully
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.account_added_ok'), 'accounts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        // find account data
        $account = Account::where('id', $id)->first();
        if (!$account) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
        }
        // find User data
        $user = User::where('role_id', 1)->where('account_id', $account->id)->first();
        if (!$user) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans(''));
        }
        // find City data
        $city = City::where('id', $account->city_id)->first();

        // find Plan data
        $plan = Plan::where('id', $account->plan_id)->first();
        if (!$plan) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'));
        }

        // find DoctorDetail data
        $account_setting = DoctorDetail::where('account_id', $account->id)->first();
        if (!$account_setting) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.doctor_details_add_err'));
        }

        return view("admin.rk-admin.accounts.show", compact('account', 'account_setting', 'city', 'plan', 'user'));
    }

    /**
     * Show the form for editing account data
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        // find account data
        $account = AccountRepository::getAccountById($id);
        if (!$account) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
        }

        // find User data
        $user = AuthRepository::getUserByRoleAndAccountId(self::ROLE_DOCTOR, $account->id);
        if (!$user) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
        }

        // get all the plans
        $plans = (new PlanRepository())->getAllPlans();
        if (!$plans) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_plans'));
        }

        $created_by = 'N/A';
        $updated_by = 'N/A';

        // find created_by user in order to used it in the form
        if ($account->created_by) {
            $created_by = AuthRepository::getUserByColumn('id', $account->created_by);
            if ($created_by) {
                $created_by = $created_by->name;
            }
        }
        // find updated_by user in order to used it in the form
        if ($account->updated_by) {
            $updated_by = AuthRepository::getUserByColumn('id', $account->created_by);
            if ($updated_by) {
                $updated_by = $updated_by->name;
            }
        }

        $account->name = $user->name;
        $account->mobile = $user->mobile;
        $account->country = $account->city->country;

        return view('admin.rk-admin.accounts.edit', compact('account', 'plans', 'created_by', 'updated_by', 'user'));
    }

    /**
     * Update the account data
     *
     * @param AccountRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AccountRequest $request, $id)
    {
        // find logged in User
        $auth_user = auth()->user();

        // find account that user logged in belongs to
        $account = AccountRepository::getAccountById($id);
        if (!$account) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
        }

        DB::beginTransaction();
        try {
            // update user name and mobile
            $user = AuthRepository::getUserByRoleAndAccountId(self::ROLE_DOCTOR, $account->id);
            if (!$user) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
            }

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            // set the updated_by user
            $user->updated_by = $auth_user->id;
            $user->update();

            // find Plan data
            $plan = (new PlanRepository())->getPlanById($account->plan_id);
            if (!$plan) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'), 'accounts.index', $account->id);
            }

            $account = (new AccountRepository())->updateAccount($account, $request->plan_id, $request->days, $plan->price_of_day, $auth_user->id);
            if (!$account) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_update_err'), 'accounts.index', $account->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_update_err'), 'accounts.index', $account->id);
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.account_update_ok'), 'accounts.index', $account->id);
    }

    /**
     * @param $id
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function publish($id, $status)
    {
        if (isset($id) && !empty($id)) {
            $account = AccountRepository::getAccountById($id);
            DB::beginTransaction();

            $checkAccountData = $this->checkAccountPublishingConstrains($id, $account);

            if (auth()->user()->role_id == self::ROLE_DOCTOR) {
                // in case of Doctor
                if (!$checkAccountData) {
                    DB::rollBack();
                    return $this->messageAndRedirect(self::STATUS_ERR, $this->publish_errors);
                }

                // send mail to Seena to notify that user completed his account
                try {
                    $data = [
                        'name' => $account->en_name,
                        'subject' => 'publish request',
                        'view' => 'emails.publish_request',
                        'to' => 'm.aman@rkanjel.com',
                    ];
                    $this->sendMailTraitFun($data);
                } catch (\Exception $e) {
                    DB::rollBack();
                    self::logErr($e->getMessage());
                    return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.send_email_failed'));
                }

                DB::commit();
                return $this->messageAndRedirect(self::STATUS_OK, trans('lang.account_is_now_pending'));
            } elseif (in_array(auth()->user()->role_id, [self::ROLE_RK_SUPER_ADMIN, self::ROLE_RK_SALES, self::ROLE_RK_ADMIN])) {
                if ($status == 'true') {
                    // in case of super admin
                    if ($account->is_published === 2 && $checkAccountData) {
                        $account = (new AccountRepository())->update($account, 'is_published', 1);
                        if (!$account) {
                            DB::rollBack();
                            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
                        }
                        DB::commit();
                        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.account_published_ok'));
                    }

                    // user did not completed his account
                    return $this->messageAndRedirect(self::STATUS_ERR, $this->publish_errors);
                } elseif ($status == 'false') {
                    $account = (new AccountRepository())->update($account, 'is_published', 0);
                    if (!$account) {
                        DB::rollBack();
                        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
                    }
                    DB::commit();
                    return $this->messageAndRedirect(self::STATUS_OK, trans('lang.account_un_published_ok'));
                }
            }
        } else {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function activate($id)
    {
        if (isset($id) && !empty($id)) {
            $user = (new \App\Http\Repositories\Api\AuthRepository())->getUserByAccount($id, self::ROLE_DOCTOR);

            (new \App\Http\Repositories\Api\AuthRepository())->activateUser($user);
            if (!$user) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
            }
            return $this->messageAndRedirect(self::STATUS_OK, trans('lang.user-activate-successfully'));
        }

        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
    }

    /**
     * Remove the specified account
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->accountRepository->account, $id);

    }

    /**
     * get all cities inside the country
     *
     * @param $id
     * @return mixed
     */
    public function countryCities($id)
    {
        $country = CountryRepository::getRecordByColumn('id', $id);
        if (!$country) {
            abort(404, 'Country not found');
        }
        $cities = (new CityRepository())->getCitiesByCountryId($country->id);
        if (!$cities) {
            abort(404, 'City not found');
        }
        return $cities;
    }

    //load blade that contain row country and city
    public function loadRow()
    {
        $countries = (new CountryRepository())->getAllCountries();
        if (count($countries) == 0) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_countries'));
        }
        return view('admin.rk-admin.accounts.country-cities', compact('countries'));
    }

    /**
     * publish Doctor Constrains
     *
     * @param $id
     * @param $account
     * @return bool
     * @throws \Exception
     */
    public function checkAccountPublishingConstrains($id, $account)
    {
        // first check the clinics
        $clinics = (new ClinicRepository())->getIdsOfAccountClinics($id);
        if (count($clinics) === 0) {
            $this->publish_errors = trans('lang.you-need-to-add-clinics');
            return false;
        }

        if ($account->type === self::ACCOUNT_TYPE_POLY) {
            // second check address in case of poly clinic
            if ($account->en_address == null || $account->ar_address == null) {
                $this->publish_errors = trans('lang.you-need-to-add-account-address');
                return false;
            }
        }

        // third check working hours
        $workingHours = (new WorkingHourRepository())->getAllWorkingHours($clinics);
        if (count($workingHours) === 0) {
            $this->publish_errors = trans('lang.you-need-to-add-workingHours');
            return false;
        }

        // check the Bio
        $doctor_details = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId($account->id);

        if ($doctor_details) {
            if ($doctor_details->en_bio == null || $doctor_details->ar_bio == null) {
                $this->publish_errors = trans('lang.you-need-to-bio');
                return false;
            }
        }

        $auth = (new \App\Http\Repositories\Api\AuthRepository())->getUserByAccount($account->id, self::ROLE_DOCTOR);
        // check the account name
        if ($auth->getOriginal('image') == 'default.png') {
            $this->publish_errors = trans('lang.you-need-to-image');
            return false;
        }

        // check the account name
        if ($account->en_name == null || $account->ar_name == null) {
            $this->publish_errors = trans('lang.you-need-to-add-account-name');
            return false;
        }

        // check services
        $services = (new AccountService())->where('account_id', $account->id)->get();
        if (!$services || count($services) <= 0) {
            $this->publish_errors = trans('lang.you-need-to-add-service');
            return false;
        }


        $account = (new AccountRepository())->update($account, 'is_published', 2);
        if (!$account || $account->is_published != 2) {
            $this->publish_errors = trans('lang.account_not_found');
            return false;
        }

        return true;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function CheckAccountCompletion(Request $request)
    {
        $auth = auth()->user();
        $account_id = $auth->account_id;

        if ($auth->account->type === 1) {
            return redirect()->route('poly-account-completion');
        }

        $steps = [];
        $active_number = -1;
        // step 1 clinics
        $clinics = (new ClinicRepository())->getIdsOfAccountClinics($account_id);
        // step 2 workingHours
        $workingHours = (new WorkingHourRepository())->getAllWorkingHours($clinics);
        // step 3 check the Bio
        $doctor_details = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId($account_id);

        // check Doctor Details
        if ($doctor_details) {
            if (
                $doctor_details->en_bio == null ||
                $doctor_details->ar_bio == null ||
                $auth->account->en_name == null ||
                $auth->account->ar_name == null ||
                $auth->account->en_name == 'No Name' ||
                $auth->account->ar_name == 'لا يوجد اسم' ||
                $doctor_details->speciality_id == null
            ) {
                $active_number = 3;
                $steps[] = ['completed' => false];
            } else {
                $steps[] = ['completed' => true];
            }
        }

        // check workingHours
        if (count($workingHours) === 0) {
            $active_number = 2;
            $steps[] = ['completed' => false];
        } else {
            $steps[] = ['completed' => true];
        }

        // check clinics
        if (count($clinics) === 0) {
            $active_number = 1;
            $steps[] = ['completed' => false];
        } else {
            $steps[] = ['completed' => true];
        }


        if ($active_number === -1) {
            // means all steps are completed Then
            (new AccountRepository())->update(auth()->user()->account, 'is_completed', 1);
            return redirect()->route('admin');
        } elseif (isset($request['clinic'])) {
            $active_number = 2;
        } else if ($active_number > 1 && $active_number < 3) {
            $active_number--;
        }


        if ($auth->role_id == self::ROLE_DOCTOR && isset($_GET['clinic'])) {
            // get the clinic for the current assistant
            $clinic = ClinicRepository::getClinicById($_GET['clinic']);
            if ($clinic) {
                // get clinic account
                $clinic_account = AccountRepository::getAccountById($clinic->account_id);

                $days = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, true);
                $upcoming_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, false);
                $day_indexes = [];

                foreach ($days as $i => $day) {
                    $day_indexes[$i] = $day->day;
                }
            }
        } else {
            $clinic = null;
            $day_indexes = [];
            $days = array();
            $clinic_account = 0;
            $upcoming_workingHours = array();
        }

        return view('admin.general.account_completion', compact('active_number', 'steps', 'clinic', 'day_indexes', 'days', 'clinic_account', 'upcoming_workingHours'));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function CheckPolyAccountCompletion(Request $request)
    {
        $auth = auth()->user();
        $account_id = $auth->account_id;

        if ($auth->account->type === 0) {
            return redirect()->route('account-completion');
        }

        $steps = [];
        $active_number = -1;

        // step 1 clinics
        $clinics = (new ClinicRepository())->getIdsOfAccountClinics($account_id);
        // step 2 workingHours
        $workingHours = (new WorkingHourRepository())->getAllWorkingHours($clinics);
        // step 3 check the Bio
        $doctor_details = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId($account_id);

        // check Doctor Details
        if ($doctor_details) {
            if (
                $doctor_details->en_bio == null ||
                $doctor_details->ar_bio == null ||
                $auth->account->en_name == null ||
                $auth->account->ar_name == null ||
                $auth->account->en_name == 'No Name' ||
                $auth->account->ar_name == 'لا يوجد اسم' ||
                $auth->account->en_address == null ||
                $auth->account->ar_address == null
            ) {
                $active_number = 3;
                $steps[] = ['completed' => false];
            } else {
                $steps[] = ['completed' => true];
            }

            // check workingHours
            if (count($workingHours) === 0) {
                $active_number = 2;
                $steps[] = ['completed' => false];
            } else {
                $steps[] = ['completed' => true];
            }

            // check clinics
            if (count($clinics) === 0) {
                $active_number = 1;
                $steps[] = ['completed' => false];
            } else {
                $steps[] = ['completed' => true];
            }

            if ($active_number === -1) {
                // means all steps are completed Then
                (new AccountRepository())->update(auth()->user()->account, 'is_completed', 1);
                return redirect()->route('admin');
            } elseif (isset($request['clinic'])) {
                $active_number = 2;
            } else if ($active_number > 1 && $active_number < 3) {
                $active_number--;
            }

            if ($auth->role_id == self::ROLE_DOCTOR && isset($_GET['clinic'])) {
                // get the clinic for the current assistant
                $clinic = ClinicRepository::getClinicById($_GET['clinic']);
                if ($clinic) {
                    // get clinic account
                    $clinic_account = AccountRepository::getAccountById($clinic->account_id);

                    $days = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, true);
                    $upcoming_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, false);
                    $day_indexes = [];

                    foreach ($days as $i => $day) {
                        $day_indexes[$i] = $day->day;
                    }
                }
            } else {
                $clinic = null;
                $days = null;
                $day_indexes = [];
                $clinic_account = null;
                $upcoming_workingHours = null;
            }
        }
        return view('admin.general.poly_account_completion', compact('active_number', 'steps', 'clinic', 'day_indexes', 'days', 'clinic_account', 'upcoming_workingHours'));
    }


    public function sendNotification(Request $request)
    {
        $list = explode(',', $request['status']);
        $user_id = $request['user_id'];

        $en_msg = 'You are a little bit away to publish your account, please add ';
        $ar_msg = 'انت علي بعد خطوات بسيطة من نشر حسابك من فضلك اضف ';

        if ($list['0'] == 'false') {
            $en_msg .= 'image, ';
            $ar_msg .= ', صوره شخصيه ';
        }

        if ($list['1'] == 'false') {
            $en_msg .= 'Bio, ';
            $ar_msg .= ', سيره ذاتيه ';
        }

        if ($list['3'] == 'false') {
            $en_msg .= 'Services,';
            $ar_msg .= ', خدمات';
        }

        (new NotificationController())->sendNotification($user_id, 'notifications', 'الاشعاارات', $en_msg, $ar_msg);

        return response()->json(['status' => true]);
    }

    /**
     *  Access from superAdmin to any Doctor
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function access($id)
    {
        \Auth::logout();
        \Auth::loginUsingId($id);
        return redirect()->route('admin');
    }
}
