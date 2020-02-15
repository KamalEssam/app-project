<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\ClinicRepository;
use App\Http\Repositories\Api\ProfileRepository;
use App\Http\Repositories\Api\SettingRepository;
use App\Http\Repositories\Api\TokenRepository;
use App\Http\Repositories\Validation\AuthValidationRepository;
use App\Http\Repositories\Web\AccountRepository;
use App\Http\Repositories\Web\CityRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\PlanRepository;
use App\Http\Repositories\Web\ServiceRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\UserTrait;
use App\Http\Traits\SmsTrait;
use App\Models\AccountService;
use Cache;
use Hash;
use Illuminate\Http\Request;
use DB;
use Event;
use App\Events\UserGenerated;
use Validator;

class AuthController extends ApiController
{
    private $settingRepository, $authRepository, $authValidationRepository, $expiration;
    use UserTrait, SmsTrait, DateTrait;

    public function __construct(Request $request, AuthRepository $authRepository, AuthValidationRepository $authValidationRepository, SettingRepository $settingRepository)
    {
        $this->authRepository = $authRepository;
        $this->settingRepository = $settingRepository;
        $this->authValidationRepository = $authValidationRepository;
        $this->setLang($request);
        $this->expiration = 30 * 60;
    }

    /**
     *  Doctor Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctorLogin(Request $request)
    {
        if (!isset($request->type)) {
            $request->type = 0;
        }

        // validate fields
        if (!$this->authValidationRepository->doctorLoginValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }
        // attempt user to login
        if ($this->authRepository->attemptLogin($request) == false) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.combination'));
        }

        $user = auth()->user();

        // if user dont exists, user is not doctor or assistant, user dont have account, user account is not compatible with the give type
        if (!$user || !in_array($user->role_id, [self::ROLE_DOCTOR, self::ROLE_ASSISTANT]) || !$user->account || $user->account->type != $request->type) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.login_unauthorized'));
        }


        // add clinic pattern if assistant login
        $assistant_clinic = (new ClinicRepository)->getClinicPattern($user);
        $user->pattern = $assistant_clinic !== false ? $assistant_clinic->pattern : 0;

        // add account type
        $doctor_account = self::getAccountById($user->account_id);
        $user->account_type = !$doctor_account ? 0 : $doctor_account->type;

        // Check if doctor or assistant
        if (!self::checkIfDoctorOrAssistant($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }
        try {
            $token = $this->authRepository->createToken($user);
        } catch (\Exception $ex) {
            return self::jsonResponse(false, self::CODE_INTERNAL_ERR, new \stdClass(), $ex->getMessage());
        }

        $logged_user = $this->authRepository->getUserData($user);

        // add clinic pattern if assistant login
        $assistant_clinic = (new ClinicRepository)->getClinicPattern($user);
        $logged_user->pattern = $assistant_clinic !== false ? $assistant_clinic->pattern : 0;

        $doctor_details = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId($doctor_account->id);

        $logged_user->ar_bio = $doctor_details->ar_bio;
        $logged_user->en_bio = $doctor_details->en_bio;
        $logged_user->services = AccountService::join('services', 'services.id', 'account_service.service_id')
            ->where('account_service.account_id', $doctor_account->id)
            ->select('account_service.service_id', 'account_service.price', 'account_service.premium_price', 'services.' . app()->getLocale() . '_name as name')
            ->get();

        if (
            $logged_user->ar_bio != null &&
            $logged_user->en_bio != null &&
            $logged_user->ar_bio != 'لا توجد بيانات للعرض' &&
            $logged_user->en_bio != 'No Data To Show' &&
            count($logged_user->services) > 0 &&
            $user->getOriginal('image') != 'default.png'
        ) {
            $logged_user->steps_completed = 1;
        } else {
            $logged_user->steps_completed = 0;
        }

        if ($user->is_active != self::ACTIVE) {
            return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.not-activated'), new \stdClass(), $logged_user, $token);
        }

        $user->increment('login_counter');

        return self::jsonResponse(true, self::CODE_OK, trans('lang.logged-on'), new \stdClass(), $logged_user, $token);
    }

    /**
     *  sign up new doctor
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function doctorSignUp(Request $request)
    {
        // validate fields
        if (!$this->authValidationRepository->doctorSignUpValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        DB::beginTransaction();
        // put the Pin
        $request['pin'] = random_int(1111, 9999);

        // create new user
        $user = $this->authRepository->createUser($request->all());

        if ($user === false) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-created-failed'));
        }
        // generate a new unique id for this user and update all counters
        try {
            Event::fire(new UserGenerated(self::ROLE_DOCTOR, $user));
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr('a : ' . $e->getMessage());
            return self::jsonResponse(false, self::CODE_INTERNAL_ERR, $e->getMessage());
        }

        // get application settings
        $setting = $this->settingRepository->getFirstSetting();
        if (!$setting) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.setting-not-found'));
        }

        //create account after user to fire event first to get account counter
        try {
            $request['days'] = 14;
            $request['plan_id'] = 1;
            $request['is_published'] = self::FALSE;
            $request['city_id'] = (new CityRepository())->getFirstCity()->id;
            $account = (new AccountRepository())->createAccount($request);
        } catch (\Exception $e) {
            self::logErr('account :  create account');
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.account_add_err'));
        }

        // create doctor details table
        try {
            (new DoctorDetailsRepository())->createDoctorDetail($account->id, null);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr('d : ' . $e->getMessage());
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.doctor_details_add_err'));
        }

        //
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
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.plan_not_found'));
        }

        // calculate the Due amount for the account plan
        $account->due_amount = $request['days'] * $plan->price_of_day;
        // calculate the Due data for the account plan
        $account->due_date = self::addDays(self::getToday(), 4);
        $account->save();

        // set password for the user
        $user = $this->authRepository->setPassword($user, $request['password']);

        if (!$user) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-created-failed'));
        }
        // create Token
        try {
            $token = $this->authRepository->createToken($user);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return self::jsonResponse(false, self::CODE_INTERNAL_ERR, [], $e->getMessage());
        }
        // in case all is ok
        DB::commit();
        $user_profile = $this->authRepository->getUserById($user->id);
        $doctor = $this->authRepository->getUserData($user_profile);

        // add account type
        $doctor_account = self::getAccountById($doctor->account_id);
        $doctor->account_type = !$doctor_account ? 0 : $doctor_account->type;

        return self::jsonResponse(true, self::CODE_CREATED, trans('lang.doctor-registered-successfully'), new \stdClass(), $doctor, $token);
    }

    /**
     *  api login service
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        // validate fields
        if (!$this->authValidationRepository->loginValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        // attempt user to login
        if ($this->authRepository->attemptLogin($request) == false) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.combination'));
        }

        $user = auth()->user();

        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }

        try {
            $token = $this->authRepository->createToken($user);
        } catch (\Exception $ex) {
            return self::jsonResponse(false, self::CODE_INTERNAL_ERR, new \stdClass(), $ex->getMessage());
        }

        // is active check is disabled for now
//        if ($user->is_active != self::ACTIVE) {
//            return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.not-activated'), new \stdClass(), $user, $token);
//        }


        $logged_user = (new ProfileRepository())->getProfile($user);

        if ($logged_user->is_premium == 1) {
            $logged_user->plan = $user->userPlan;
            $logged_user->plan->expiry_date = $user->expiry_date;
        }

        // store number of times user logged
        $user->increment('login_counter');

        return self::jsonResponse(true, self::CODE_OK, trans('lang.logged-on'), new \stdClass(), $logged_user, $token);
    }

    /**
     *  sign up new patient user
     *
     * @param Request $request
     * @param SettingRepository $settingRepository
     * @return mixed
     * @throws \Exception
     */
    public function signUp(Request $request, SettingRepository $settingRepository)
    {
        // get social register request data
        $types = [0 => 'facebook', 1 => 'google'];
        if (!empty($request['social_id']) && !empty($request['type'])) {
            if (!in_array($request->type, [0, 1])) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
            }
            $column = $types[$request->type] . '_id';
            // update if social register
            $exist_user = $this->authRepository->getUserByMobile($request['mobile']);

            if ($exist_user) {
                if ($exist_user->role_id == self::ROLE_USER) {
                    unset($request['password']);
                    $this->authRepository->updateUser($exist_user, $request);
                    if (!empty($request['social_id']) && !empty($request['type'])) {
                        $social_user = $this->authRepository->updateColumn($exist_user, $column, $request['social_id']);
                        if ($social_user == false) {
                            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
                        }

                    }
                    return self::jsonResponse(false, self::CODE_RECORD_EXISTS, trans('lang.user-already-exist'));
                }
            }
        }

        // validate fields
        if (!$this->authValidationRepository->signupValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }
        DB::beginTransaction();
        // create new user

        // put the Pin
        $request['pin'] = random_int(1111, 9999);

        $user = $this->authRepository->createUser($request->all());
        if ($user === false) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-created-failed'));
        }

        try {
            Event::fire(new UserGenerated(self::ROLE_USER, $user));
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_INTERNAL_ERR, $e->getMessage());
        }

        // get first setting
        $setting = $settingRepository->getFirstSetting();
        if (!$setting) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.setting-not-found'));
        }
        // update if social register
        if (!empty($request['social_id']) && !empty($request['type'])) {
            $user = $this->authRepository->updateColumn($user, $column, $request['social_id']);
            if (!$user) {
                DB::rollBack();
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }
        }

        // update user and set unique id
        $user = $this->authRepository->updateAfterCreate($setting->user_counter, $user);

        if ($user == null) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.registration-failed'));
        }
        // create Token
        try {
            $token = $this->authRepository->createToken($user);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return self::jsonResponse(false, self::CODE_INTERNAL_ERR, [], $e->getMessage());
        }

        DB::commit();

        if (isset($request['shareUserUniqueId']) && $request['shareUserUniqueId'] !== -1) {
            // check if user registered through share link or not (if it is then give share user and shared user month premium as free)
            $this->setPremiumThroughShare($user, $request['shareUserUniqueId']);
        }

        $user_profile = $this->authRepository->getUserById($user->id);
        return self::jsonResponse(true, self::CODE_CREATED, trans('lang.registered-successfully'), new \stdClass(), $this->authRepository->getUserData($user_profile), $token);
    }

    /**
     *  / check if user registered through share link or not (if it is then give share user and shared user month premium for free)
     *
     * @param $user
     * @param $sharingUserId
     */
    private function setPremiumThroughShare($user, $sharingUserId)
    {
        try {
            // now check if the link sender is exists
            $sharingUser = \App\Http\Repositories\Web\AuthRepository::getUserByColumn('unique_id', $sharingUserId);
            if ($sharingUser) {
                // the new client is now premium
                (new \App\Http\Repositories\Web\AuthRepository())->updateUserPremium($user->id, 1, 1, 1);

                // check if the user is not premium or expired
                if ($sharingUser->expiry_date == null || $sharingUser->expiry_date <= now()->format('Y-m-d')) {
                    (new \App\Http\Repositories\Web\AuthRepository())->updateUserPremium($sharingUser->id, 1, 1, 1);
                }

                // create record in the database for Every Success App Share
                DB::table('share_app_logs')->insert([
                    'sender_id' => '',
                    'receiver_id' => '',
                    'created_at' => now()->format('Y-m-d H:i:s'),
                ]);

            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
    }

    /**
     *  activate user account
     *
     * @param Request $request
     * @return mixed
     */

    public function activateAccount(Request $request)
    {
        $user = auth()->guard('api')->user();

        // activate user
        $activate_user = $this->authRepository->activateUser($user);

        if ($activate_user == false) {
            return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.failed-activate'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.user-activate-successfully'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function forgotPassword(Request $request)
    {
        // validate fields
        if (!$this->authValidationRepository->forgetPasswordValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors(), 'FAILED');
        }
        $user = $this->authRepository->getUserWithMobile($request['mobile']);
        if ($user == false) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.user-not-found'));
        }

        DB::beginTransaction();
        // send SMS Message
        try {
            $request2 = new Request();
            $request2['pin'] = random_int(1111, 9999);
            $request2->pin = $request2['pin'];
            $this->authRepository->updateUser($user, $request2);

            if (app()->getLocale() == 'en') {
                $msg = "Your Verification Code Is $user->pin";
                $lang = 1;
            } else {
                $msg = " كود التفعيل الخاص بكم هو $user->pin";
                $lang = 2;
            }
            self::sendRklinicSmsMessage($user->mobile, $msg, $lang);

        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return self::jsonResponse(false, self::CODE_FAILED, [], trans('lang.failed-activation-message'));
        }
        DB::commit();
        // Send Only Verification Code Which Sent To User Through SMS message
        return self::jsonResponse(true, self::CODE_OK, trans('lang.verification_sent'), [], '0000');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function resetPassword(Request $request)
    {
        // validate fields
        if (!$this->authValidationRepository->resetPasswordValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        // check user existence
        $user = $this->authRepository->getUserWithMobile($request['mobile']);

        if ($user == false) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.user-not-found'));
        }
        // update password
        $forget_password = $this->authRepository->updatePassword($user, $request['new_password']);

        if ($forget_password == false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-update-password'));
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.password-updated-successfully'));

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function changePassword(Request $request)
    {
        // validate fields
        if (!$this->authValidationRepository->changePasswordValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        // get user
        $user = auth()->guard('api')->user();
        // if old password = new password
        if ($request->old_password == $request->new_password) {
            return self::jsonResponse(false, self::CODE_SAME_PASSWORD, trans('lang.same-password'));
        }
        // if old password !=  user password
        if (!Hash::check($request->old_password, $user->password)) {
            return self::jsonResponse(false, self::CODE_NOT_MATCH, trans('lang.old-password-not-match'));
        }
        //change user password
        $change_password = $this->authRepository->updatePassword($user, $request->new_password);
        if (!$change_password) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        // notify user that your password changed
        return self::jsonResponse(true, self::CODE_OK, trans('lang.password-changed-successfully'));
    }

    /**
     * log user out from this device
     *
     * @param Request $request
     * @param TokenRepository $tokenRepository
     * @return mixed
     */
    public function logout(Request $request, TokenRepository $tokenRepository)
    {
        // validate fields
        if (!$this->authValidationRepository->logoutValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        // get user token using user_id and serial
        $user_token = $tokenRepository->getTokenByUserAndSerial($request);
        if ($user_token) {
            $user_token->delete();
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.user-log-out-successfully'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function resendVerificationCode(Request $request)
    {
        $auth = auth()->guard('api')->user();

        // In Case Of Reset Password , User Is Not Authenticated
        if ($auth == null) {
            try {
                $auth = $this->authRepository->getUserByMobile($request['mobile']);
                if (!$auth) {
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-not-found'), []);
                }
            } catch (\Exception $e) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-not-found'), []);
            }
        }

        $key = 'user_message_' . $auth->id;

        if (Cache::has($key)) {
            if (Cache::get($key) >= 3) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.wait-next-message'), []);
            }
            $attemps = Cache::get($key) + 1;
            Cache::forget($key);  // remove previous value
            Cache::remember($key, $this->expiration, function () use ($attemps) {
                // get the number of messages sent
                return $attemps;
            });
        } else {
            Cache::remember($key, $this->expiration, function () {
                // get the number of messages sent
                return 2;
            });
        }


        $request_2 = new Request();
        $request_2['pin'] = random_int(1001, 9999);

        DB::beginTransaction();
        try {
            $this->authRepository->updateUser($auth, $request_2);
        } catch (\Exception $e) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.verification-failed'), []);
        }

        //     send SMS Message
        try {
            if (app()->getLocale() == 'en') {
                $msg = "Your Verification Code Is $auth->pin";
                $lang = 1;
            } else {
                $msg = " كود التفعيل الخاص بكم هو $auth->pin";
                $lang = 2;
            }
            self::sendRandomSmsMessage($auth->mobile, $msg, $lang);

        } catch (\Exception $e) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-activation-message'), []);
        }

        DB::commit();
        return self::jsonResponse(true, self::CODE_OK, trans('lang.code-generated'), [], (string)$auth->pin);
    }


    /**
     *  code verification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request)
    {
        logger('the pin is' . $request['pin']);

        $auth = auth()->guard('api')->user();
        // In Case Of Reset Password , User Is Not Authenticated

        if ($request->has('mobile')) {
            $auth = (new AuthRepository())->getUserByMobile($request['mobile']);
        }

        $activate_account = true;

        if ($auth == null) {
            $activate_account = false;
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-not-found'), []);
        }

        if ($activate_account) {
            $activate_user = $this->authRepository->activateUser($auth);
            if ($activate_user == false) {
                return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.failed-activate'));
            }
        }

        // validate pin
        if (!$this->authValidationRepository->verifyCode($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        if ($auth->pin != $request['pin']) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.code_not_match'), []);
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.code-verified'), []);
    }


    /**
     *  change the notification status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userNotificationStatus(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (is_null($user)) {
            return self::jsonResponse(true, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'), [], new \stdClass());
        }

        // validate fields
        if (!$this->authValidationRepository->notificationStatusValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        if (!$this->authRepository->updateNotification($user, $request->status)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-do-this-action'), [], new \stdClass());
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.notifications-switched'), [], new \stdClass());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeDoctorData(Request $request)
    {
        $user = auth()->guard('api')->user();
        if ($user == null) {
            return self::jsonResponse(true, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'), [], new \stdClass());
        }

        // validate fields
        if (!$this->authValidationRepository->doctorUpdateDate($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }

        // if user dont exists, user is not doctor or assistant, user dont have account, user account is not compatible with the give type
        if (!$user || $user->role_id != self::ROLE_DOCTOR) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.unauthorized'));
        }

        try {
            $doctor_details = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId($user->account_id);

            if ($request->has('image')) {
                // get base64 image from user
                (new ProfileRepository())->setImage($user, $request->get('image'));
            }
            if (!$doctor_details) {
                return self::jsonResponse(true, self::CODE_FAILED, trans('lang.whoops'), [], new \stdClass());
            }
            (new DoctorDetailsRepository())->updateBio($doctor_details, $request->get('ar_bio'), $request->get('en_bio'));

        } catch (\Exception $e) {
            return self::jsonResponse(true, self::CODE_FAILED, trans('lang.whoops'), [], new \stdClass());

        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.update_data'), [], new \stdClass());
    }

    /**
     *  get list of services (id, name)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllServices(Request $request)
    {
        try {
            $services = (new ServiceRepository())->apiGetServices();
            if (!$services) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.whoops'), [], new \stdClass());
            }

            return self::jsonResponse(true, self::CODE_OK, trans('lang.total_services'), [], $services);

        } catch (\Exception $e) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.whoops'), [], new \stdClass());
        }
    }

    /**
     *  set list of services
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setServices(Request $request)
    {
        $user = auth()->guard('api')->user();
        if ($user == null) {
            return self::jsonResponse(true, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'), [], new \stdClass());
        }

        // validate fields
        if (!$this->authValidationRepository->setServices($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
        }
        // set services
        $doctorAccount = AccountRepository::getAccountById($user->account_id);

        if (is_string($request->get('services'))) {
            $services = json_decode($request->get('services'), true);
        } else {
            $services = $request->get('services');
        }

        $data = array();
        // get old services first
        foreach ($services as $service) {
            if ($service['price'] >= $service['premium_price']) {
                $data[$service['service_id']] =
                    array(
                        'price' => $service['price'],
                        'premium_price' => $service['premium_price']
                    );
            }
        }

        $doctorAccount->services()->sync($data);
        return self::jsonResponse(true, self::CODE_OK, trans('lang.service_added_ok'), [], new \stdClass());

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePremium(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|min:0|max:1',
        ]);
        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }

        $user = auth()->guard('api')->user();
        if ($user == null) {
            return self::jsonResponse(true, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'), [], new \stdClass());
        }

        if ($user->is_premium == $request->get('status')) {
            if ($user->is_premium == 1) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.already_premium'));
            }
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.already_not_premium'));
        }

        // Check if patient
        if (self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }
        $change_premium = $this->authRepository->updateColumn($user, 'is_premium', $request->get('status'));
        if (!$change_premium) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.premium_failed'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.become_premium'));
    }
}
