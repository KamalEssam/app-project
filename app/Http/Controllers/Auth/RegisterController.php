<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserGenerated;
use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AccountRepository;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\PlanRepository;
use App\Http\Repositories\Web\SettingRepository;
use App\Http\Repositories\Web\SpecialityRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\MailTrait;
use App\Http\Traits\SmsTrait;
use App\Models\LogAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Event;
use DB;
use Hash;

class RegisterController extends WebController
{
    use MailTrait, DateTrait, SmsTrait;
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/manager';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'mobile' => $data['mobile'],
            'type' => $data['type'],
            'is_active' => !self::ACTIVE
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'mobile' => 'required|unique:users,mobile',
            'type' => 'required|numeric|min:0|max:1',
        ]);

        DB::beginTransaction();
        //create user for the account
        try {
            // create new user
            $user = (new AuthRepository())->createUSer($request, !self::ACTIVE);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr("c : " . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-created-failed'));
        }

        // generate a new unique id for this user and update all counters
        try {
            Event::fire(new UserGenerated(self::ROLE_DOCTOR, $user));
        } catch (\Exception $e) {
            DB::rollback();

            self::logErr("a : " . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.unique_id_error'));
        }

        // get application settings
        $setting = SettingRepository::getFirstSetting();
        if (!$setting) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_settings'));
        }

        //create account after user to fire event first to get account counter
        try {
            $request['days'] = 14;
            $request['plan_id'] = 1;
            $request['is_published'] = self::FALSE;
            $request['city_id'] = (new CityRepository)->getFirstCity()->id;
            $account = (new AccountRepository())->createAccount($request);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_add_err'));
        }

        //
        $speciality_id = null;
        // create doctor details table
        try {
            (new DoctorDetailsRepository())->createDoctorDetail($account->id, $speciality_id);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr('d : ' . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.doctor_details_add_err'));
        }

        $account->unique_id = 'RK_ACC_' . (999 + $setting->account_counter);
        $account->save();

        //update user data
        $user->unique_id = $account->unique_id;
        $user->account_id = $account->id;
        $user->save();

        // get the plan by id
        $plan = (new PlanRepository())->getPlanById($account->plan_id);
        if (!$plan) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'));
        }

        // calculate the Due amount for the account plan
        $account->due_amount = $request['days'] * $plan->price_of_day;
        // calculate the Due data for the account plan
        $account->due_date = self::addDays(self::getToday(), 4);
        $account->save();

        // set password for the user
        $user = (new AuthRepository())->setPassword($user, $request['password']);

        if (!$user) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-created-failed'));
        }

        // in case all is ok
        DB::commit();
        // login the user
        $this->guard()->login($user);

        return redirect()->route('admin');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showSalesRegistrationForm()
    {
        return view('auth.sales-register');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function Salesregister(Request $request)
    {
        $this->validate($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
                'mobile' => 'required|unique:users,mobile',
                'type' => 'required|numeric|min:0|max:1',
                'sales_email' => 'required|email|exists:users,email'
            ]
        );

        DB::beginTransaction();
        //create user for the account
        try {
            // create new user
            $user = (new AuthRepository())->createUSer($request, self::ACTIVE);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr("c : " . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-created-failed'));
        }

        // generate a new unique id for this user and update all counters
        try {
            Event::fire(new UserGenerated(self::ROLE_DOCTOR, $user));
        } catch (\Exception $e) {
            DB::rollback();

            self::logErr("a : " . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.unique_id_error'));
        }

        // get application settings
        $setting = SettingRepository::getFirstSetting();
        if (!$setting) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_settings'));
        }

        //create account after user to fire event first to get account counter
        try {
            $request['days'] = 14;
            $request['plan_id'] = 1;
            $request['is_published'] = self::FALSE;
            $request['city_id'] = (new CityRepository)->getFirstCity()->id;
            $account = (new AccountRepository())->createAccount($request);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_add_err'));
        }

        //
        $speciality_id = null;

        // create doctor details table
        try {
            (new DoctorDetailsRepository())->createDoctorDetail($account->id, $speciality_id);
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr("d : " . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.doctor_details_add_err'));
        }

        $account->unique_id = "RK_ACC_" . (999 + $setting->account_counter);
        $account->save();

        //update user data
        $user->unique_id = $account->unique_id;
        $user->account_id = $account->id;
        $user->save();

        // get the plan by id
        $plan = (new PlanRepository())->getPlanById($account->plan_id);
        if (!$plan) {
            DB::rollback();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.plan_not_found'));
        }

        // calculate the Due amount for the account plan
        $account->due_amount = $request['days'] * $plan->price_of_day;
        // calculate the Due data for the account plan
        $account->due_date = self::addDays(self::getToday(), 4);
        $account->save();

        // set password for the user
        $user = (new AuthRepository())->setPassword($user, $request['password']);

        if (!$user) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-created-failed'));
        }

        try {
            // add record for sales
            $sales = AuthRepository::getUserByColumn('email', $request->sales_email);

            if ($sales->role_id != self::ROLE_RK_SALES) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.email-validation'));
            }

            if ($sales) {
                LogAccount::create([
                    'sales_id' => $sales->id,
                    'account_id' => $account->id
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            self::logErr("sales : " . $e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.whoops'));
        }

        // in case all is ok
        DB::commit();
        // login the user
        $this->guard()->login($user);

        return redirect()->route('admin');
    }
}
