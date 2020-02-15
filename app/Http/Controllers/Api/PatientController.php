<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\PatientRepository;
use App\Http\Repositories\Api\SettingRepository;
use App\Http\Repositories\Validation\PatientValidationRepository;
use App\Http\Repositories\Web\PatientPlanRepository;
use App\Http\Repositories\Web\PremiumRequestRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\SmsTrait;
use App\Http\Traits\UserTrait;
use App\Models\RegisteredDoctors;
use DB;
use Event;
use Illuminate\Http\Request;
use App\Events\UserGenerated;
use Log;

class PatientController extends ApiController
{
    use UserTrait, DateTrait, SmsTrait;
    private $authRepository, $patientRepository, $patientValidationRepository;

    /**
     * PatientController constructor.
     * @param Request $request
     * @param AuthRepository $authRepository
     * @param PatientRepository $patientRepository
     * @param PatientValidationRepository $patientValidationRepository
     */
    public function __construct(Request $request, AuthRepository $authRepository, PatientRepository $patientRepository, PatientValidationRepository $patientValidationRepository)
    {
        $this->patientValidationRepository = $patientValidationRepository;
        $this->authRepository = $authRepository;
        $this->patientRepository = $patientRepository;
        $this->setLang($request);
    }

    /**
     * get all patients follow this doctor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientsRelatedToThisDoctor(Request $request)
    {
        $user = auth()->guard('api')->user();
        // Check if doctor or assistant
        if (!self::checkIfDoctorOrAssistant($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }
        // get doctor that assistant or doctor related to him
        $doctor = (new AuthRepository)->getUserByAccount($user->account_id, ApiController::ROLE_DOCTOR);
        if (!$doctor) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.doctor-not-found'));
        }
        $patients = $this->patientRepository->getPatientsRelatedToThisDoctor($doctor, $request);

        if ($patients === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.patients'), new \stdClass(), $patients);
    }

    /**
     * @param Request $request
     * @param SettingRepository $settingRepository
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function addPatient(Request $request, SettingRepository $settingRepository)
    {
        $auth_user = auth()->guard('api')->user();
        // Check if doctor or assistant
        if (self::checkIfAssistant($auth_user) === 'false') {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }
        try {
            $doctor_account = (new AuthRepository)->getAccountById($auth_user->account_id);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }

        // validate fields
        if (!$this->patientValidationRepository->addPatientValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->patientValidationRepository->getFirstError(), $this->patientValidationRepository->getErrors(), new \stdClass());
        }
        DB::beginTransaction();

        $user = $this->authRepository->createUser($request->all());
        if ($user == null) {
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
        // update user and set unique id
        $user = $this->authRepository->updateAfterCreate($setting->user_counter, $user);

        if (is_null($user)) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.registration-failed'));
        }

        // send set password msg
        if ($user->password == null) {
            $link = route('getPasswordForm', ['id' => $user->unique_id]);
            if (app()->getLocale() == 'en') {
                $msg = 'you have been added by clinic dr ' . $auth_user->account['en_name'] . ', you can now review your visits please complete registration steps ' . $link;
                $lang = self::LANG_EN;
            } else {
                $msg = ' تم اضافتك من قبل عيادة الدكتور' . $auth_user->account['ar_name'] . ' يمكنك الان متابعة زياراتك برجاء استكمال التسجبل ' . $link;
                $lang = self::LANG_AR;
            }

            // send SMS Message
            try {
                self::sendRklinicSmsMessage($user->mobile, $msg, $lang);
            } catch (\Exception $e) {
                DB::rollBack();
                self::logErr($e->getMessage());
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-send-sms'), []);
            }
        }
        // Activate the current Doctor
        try {
            RegisteredDoctors::create([
                'account_id' => $auth_user->account_id,
                'user_id' => $user->id,
                'active' => 1,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
        }

        DB::commit();

        $user_profile = $this->authRepository->getUserById($user->id);
        if ($user_profile === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-not-found'));
        }
        return self::jsonResponse(true, self::CODE_CREATED, trans('lang.add-patient-successfully'), new \stdClass(), $this->authRepository->getUserData($user_profile));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientPlans()
    {
        $plans = (new PatientPlanRepository())->getApiPlans();

        $response = new \stdClass();

        $auth = auth()->guard('api')->user();
        if ($auth != null) {
            // check for request for this user
            $user_request = (new PremiumRequestRepository())->getRequestByUserIdAndStatus($auth->id, '-1');

            $discount = new \stdClass();
//            // If user has promoCode
//            if ($auth->premium_code_id) {
//                $promo = (new PremiumPromoRepository())->getPromoById($auth->premium_code_id);
//                if ($promo) {
//                    $diff = now()->diffInDays(Carbon::parse($promo->expiry_date), false);
//                    if ($diff > 0) {
//                        // check if user has used this promo or not
//                        if ((new PremiumPromoRepository())->checkIfUserUsedPromoCode($auth->id, $promo->id)) {
//                            // TODO :: remove code
//                            $response->promo = null;
//                        } else {
//                            $response->promo = $promo;
//                            $discount->type = $promo->discount_type;
//                            $discount->amout = $promo->discount;
//                        }
//                    } else {
//                        // TODO :: remove code
//                        $response->promo = null;
//                    }
//                } else {
//                    $response->promo = null;
//                }
//            } else {
//                $response->promo = null;
//            }

//
//            foreach ($plans as $plan) {
//                if ($user_request) {
//                    if ($plan->id == $user_request->plan_id) {
//                        $plan->status = 1;
//                    } else {
//                        $plan->status = 0;
//                    }
//                }
//                if (isset($discount->type)) {
//                    if ($discount->type == 0) {
//                        // money
//                        $plan->new_price = $plan->price - $discount->amout;
//                        $plan->discount = $discount->amout . trans('lang.money_off');
//                    } else {
//                        $plan->new_price = $plan->price - ($plan->price * (1 - ($discount->amout / 100)));
//                        $plan->discount = $discount->amout . trans('lang.percentage_off');
//                    }
//                }
//            }
        }
        $response->plans = $plans;

        return self::jsonResponse(true, self::CODE_OK, trans('lang.total_plans'), new \stdClass(), $response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPremiumRequest(Request $request)
    {
        $user = auth()->guard('api')->user();
        // Check if doctor or assistant
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }

        // validate fields for the premium request
        if (!$this->patientValidationRepository->premiumRequest($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->patientValidationRepository->getFirstError(), $this->patientValidationRepository->getErrors(), new \stdClass());
        }

        // check if user has pending request or not
        $hasRequest = (new PremiumRequestRepository())->getRequestByUserIdAndStatus($user->id);
        if ($hasRequest) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.has-request'));
        }

        // check if the points is enough or not
        $plan = (new PatientPlanRepository())->getPlanById($request->plan_id);

        if ($user->user_plan_id == $plan->id) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.already-on-this-plan'));
        }

        // using CashBack Type
        if ($request->type == 1) {
            if ($plan) {
                if ($user->cash_back >= $plan->price) {
                    try {
                        // Update use Status
                        (new \App\Http\Repositories\Web\AuthRepository())->updateUserPremium($user->id, 1, $plan->id, 1);
                        // remove points from user total points
                        (new AuthRepository())->updateColumn($user, 'cash_back', $user->cash_back - $plan->price);
                        return self::jsonResponse(true, self::CODE_OK, trans('lang.request-with-premium'));
                    } catch (\Exception $e) {
                        // something error
                    }
                }
            }
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.dont-have-enough-cash'));
        } else {
            if ($request->has('transaction_id') && $request->get('transaction_id') != -1) {
                try {
                    // Update use Status
                    (new \App\Http\Repositories\Web\AuthRepository())->updateUserPremium($user->id, 1, $plan->id, $plan->months);
                    // remove points from user total points

                    (new PremiumRequestRepository())->createPremiumRequest([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'approval' => 1,
                        'promo_code_id' => null,
                        'transaction_id' => $request['transaction_id']
                    ]);

                    return self::jsonResponse(true, self::CODE_OK, trans('lang.request-with-premium'));
                } catch (\Exception $e) {
                    // something error
                }
            } else {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.transaction-not-found'));
            }
        }
//
//        $plan = (new PatientPlanRepository())->getPlanById($request->plan_id);
//        $due_date = $plan->price;
//        if ($user->premium_code_id) {
//            $promo = (new PremiumPromoRepository())->getPromoById($user->premium_code_id);
//            if ($promo) {
//                $diff = now()->diffInDays(Carbon::parse($promo->expiry_date), false);
//                if ($diff > 0) {
//                    // check if user has used this promo or not
//                    if ((new PremiumPromoRepository())->checkIfUserUsedPromoCode($user->id, $promo->id)) {
//                        // TODO :: remove code
//                    } else {
//                        $promo_code_id = $promo->id;
//                        // calculate new price
//                        if ($promo->discount_type == 0) {
//                            $due_date = $plan->price - $promo->discount;
//                        } else {
//                            $due_date = $plan->price - ($plan->price * ($promo->discount / 100));
//                        }
//                    }
//                } else {
//                    // TODO :: remove code
//                    $promo_code_id = null;
//                }
//            } else {
//                $promo_code_id = null;
//            }
//        } else {
//            $promo_code_id = null;
//        }

//        $newRequest = (new PremiumRequestRepository())->createPremiumRequest([
//            'user_id' => $user->id,
//            'plan_id' => $plan->id,
//            'approval' => -1,
//            'promo_code_id' => null,
//            'due_amount' => $due_date
//        ]);

//        if ($newRequest) {
//            return self::jsonResponse(true, self::CODE_OK, trans('lang.request-created'));
//        }
        return self::jsonResponse(false, self::CODE_FAILED, trans('lang.request-created-failed'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelPremiumRequest(Request $request)
    {
        $user = auth()->guard('api')->user();
        // Check if doctor or assistant
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'));
        }

        // check if there is request and cancel
        $user_requests = (new PremiumRequestRepository())->getUserRequestsByUserId($user->id);
        if ($user_requests) {
            foreach ($user_requests as $user_request) {
                (new PremiumRequestRepository())->SetRequestStatus($user_request, 0);
            }
        }

        if ((count($user_requests) == 0) && is_null($user->user_plan_id)) {
            return self::jsonResponse(true, self::CODE_OK, trans('lang.cancel_request_send_not_valid'));
        }

        try {
            // remove the plan from user and set the is_premium = 0 and expiry_date to day before now
            (new \App\Http\Repositories\Web\AuthRepository())->updateUserPremium($user->id, 0, null, -1);
        } catch (\Exception $e) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.error'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.cancel_request_send'));
    }

}
