<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\CashBackRepository;
use App\Http\Repositories\Api\ClinicRepository;
use App\Http\Repositories\Api\DoctorRepository;
use App\Http\Repositories\Api\NotificationRepository;
use App\Http\Repositories\Api\PromoCodeRepository;
use App\Http\Repositories\Api\ReservationRepository;
use App\Http\Repositories\Api\ReviewRepository;
use App\Http\Repositories\Api\WorkingHourRepository;
use App\Http\Repositories\Validation\AuthValidationRepository;
use App\Http\Repositories\Validation\ReservationValidationRepository;
use App\Http\Repositories\Web\OfferRepository;
use App\Http\Repositories\Web\TokenRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SmsTrait;
use App\Http\Traits\UserTrait;
use App\Models\ReservationServices;
use App\Models\ReservationsPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Validator;

class ReservationController extends ApiController
{
    private $reservationRepository, $reservationValidationRepository, $authValidationRepository;
    use DateTrait, UserTrait, NotificationTrait, SmsTrait;

    /**
     * ReservationController constructor.
     * @param Request $request
     * @param ReservationRepository $reservationRepository
     * @param ReservationValidationRepository $reservationValidationRepository
     */
    public function __construct(Request $request, ReservationRepository $reservationRepository, ReservationValidationRepository $reservationValidationRepository, AuthValidationRepository $authValidationRepository)
    {
        $this->authValidationRepository = $authValidationRepository;
        $this->reservationRepository = $reservationRepository;
        $this->reservationValidationRepository = $reservationValidationRepository;
        $this->setLang($request);
    }

    /**
     * create reservation and update data
     * @param $request
     * @param $clinic
     * @param $user
     * @param null $largest_queue
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    private function createUpdateReservationData($request, $clinic, $user, $largest_queue = null)
    {
        $auth_user = auth()->guard('api')->user();

        DB::beginTransaction();

        $promoCode = null;
        // check promo-code existence
        if ($request->has('promo-code') && $request->get('promo-code') != -1) {
            // check code validation and usage
            $promoCode = (new PromoCodeRepository())->getCodeByName($request['promo-code']);
            if ($promoCode) {
                $request['promo_code_id'] = $promoCode->id;
            }
        }

        $reservation = $this->reservationRepository->createReservation($request);
        if ($reservation == false) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-created-failed'));
        }

        $doctor = User::where('account_id', $clinic->account_id)->where('role_id', self::ROLE_DOCTOR)->first();
        if (!$doctor) {
            return ApiController::catchExceptions('Doctor not found');
        }

        // check if both doctor and patient are premium
        $isBothPremium = $doctor->is_premium && $auth_user->is_premium;
        // add Services
        if (is_string($request['services'])) {
            $request['services'] = json_decode($request['services']);
        }

        if (isset($request['services']) && count($request['services']) > 0) {
            try {
                foreach ($request['services'] as $service) {
                    // get the service if exists
                    $doctor_service = DB::table('account_service')
                        ->join('services', 'services.id', 'account_service.service_id')
                        ->where('account_service.id', $service)
                        ->select('services.ar_name', 'services.en_name', 'premium_price', 'price')
                        ->first();
                    if ($doctor_service) {
                        ReservationServices::create([
                            'reservation_id' => $reservation->id,
                            'ar_name' => $doctor_service->ar_name,
                            'en_name' => $doctor_service->en_name,
                            'price' => $isBothPremium ? $doctor_service->premium_price : $doctor_service->price,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_services'));
            }
        }

        // store the fees in the the database
        $payment = $this->reservationRepository->getReservationFees($request['services'] ?? null, $clinic->id, $reservation->type, $reservation->offer_id ?? null, $user);
        if ($payment) {
            // get the value of discount by promoCode if exists
            $promoDiscount = 0;
            if ($promoCode) {
                if ($promoCode->discount_type == 0) {
                    $promoDiscount = $promoCode->discount;
                } else {
                    $promoDiscount = ($promoCode->discount / 100) * $payment->total_fees;
                }
            }

            // create record for reservation fees in the database
            (new ReservationsPayment())->create([
                'reservation_id' => $reservation->id,
                'offer' => (isset($payment->offer) && is_object($payment->offer)) ? $payment->offer->price : 0,
                'fees' => $payment->subtotal_fees ?? 0,       // also called suc total
                'discount' => $payment->discount_money ?? 0,     // amount of money deducted due to premium-ship
                'total' => $payment->total_fees - $promoDiscount,
                'promo' => $promoDiscount  // amount of money deducted due to promo-code
            ]);
        }

        // add cashBack Request in case of Online Payment
        if ($auth_user->role_id == self::ROLE_USER && $request->payment_method == self::METHOD_INSTALLMENT) {
            if ($request->has('offer_id') && !empty($request->offer_id)) {
                $cashBack = (new CashBackRepository())->requestCashBackForInstallmentwithOffer($auth_user->id, $reservation->id, $clinic->id, $clinic->account_id, $payment->total_fees);
            } else {
                $cashBack = (new CashBackRepository())->requestCashBackForInstallmentwithOutOffer($auth_user->id, $reservation->id, $clinic->id, $clinic->account_id, $payment->total_fees);
            }
            if (!$cashBack) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed_to_request_cash_back'));
            }
        }

        // add doctor to patient followed doctors
        (new DoctorRepository())->addDoctorToFavouriteList($user, $clinic->account_id);

        // if has offer the update the offer count
        if ($request->has('offer_id') && !empty($request->offer_id)) {
            // add the offer usage
            (new OfferRepository())->ApiIncreaseOffersUsage($request->offer_id);
        }

        //update reservation status and created by
        if ($largest_queue) {
            $reservation = $this->reservationRepository->updateReservationData($reservation, $user->id, $largest_queue->queue);
        } else {
            $reservation = $this->reservationRepository->updateReservationData($reservation, $user->id);
        }

        if ($reservation === false) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        if ($auth_user->role_id === self::ROLE_ASSISTANT) {
            $update_created_by = (new AuthRepository)->setCreatedBy($reservation, $auth_user->id);
            if ($update_created_by === false) {
                DB::rollBack();
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }
        }
        // notification when reschedule
        if ($auth_user->role_id == self::ROLE_USER) {
            // single doctor
            if ($clinic->account->type == 0) {
                if ($request->reservation_id) {
                    $request['ar_message'] = 'لقد قام ' . $user->name . ' بتغيير ميعاد حجزه';
                    $request['en_message'] = $user->name . ' rescheduled his reservation';
                } else {
                    $request['ar_message'] = 'لقد قام ' . $user->name . ' باضافه حجز جديد';
                    $request['en_message'] = $user->name . ' made new reservation';
                }
                $request['multicast'] = 2;
                $request['receiver_id'] = $clinic->id;
                $request['url'] = '/reservations/all?notification=';
            } else {
                // poly
                if ($request->reservation_id) {
                    $request['ar_message'] = 'لقد قام ' . $user->name . ' بتغيير ميعاد حجزه فى عياده' . $clinic->ar_name;
                    $request['en_message'] = $user->name . ' rescheduled his reservation in clinic ' . $clinic->en_name;
                } else {
                    $request['ar_message'] = 'لقد قام ' . $user->name . ' باضافه حجز جديد' . $clinic->ar_name;
                    $request['en_message'] = $user->name . ' made new reservation in clinic' . $clinic->en_name;
                }
                // multicast for ploy-doctor
                $request['multicast'] = 1;
                $request['receiver_id'] = (new AuthRepository())->getUserByAccount($clinic->account_id, self::ROLE_DOCTOR)->id;
                $request['url'] = '/reservations/all?clinic=' . $clinic->id . '&notification=';
            }

            // send notification
            $request['sender_id'] = $user->id;
            $request['en_title'] = $clinic->account['en_name'];
            $request['ar_title'] = $clinic->account['ar_name'];
            $request['object_id'] = $reservation->id;
            $request['table'] = 'reservations';
            try {
                (new NotificationController($request))->pushAdminNotification($request);
            } catch (\Exception $e) {
                self::logErr($e->getMessage());
                DB::rollBack();
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-do-this-action'));
            }
        } else if (($auth_user->role_id == self::ROLE_ASSISTANT) && isset($request->reservation_id)) {

            // send notification for the user that his reservation rescheduled
            $receiver = (new AuthRepository())->getUserById($reservation->user_id);
            if ($receiver) {
                if (!$receiver->lang) {
                    $lang = 'en';
                } else {
                    $lang = $receiver->lang;
                }

                // create notification to be pushed to user who changed his reservation
                $notification_data = [
                    'multicast' => 0, // for user
                    'sender_id' => $auth_user->id,
                    'receiver_id' => $reservation->user_id,
                    'en_title' => $auth_user->account['en_name'],
                    'ar_title' => $auth_user->account['ar_name'],
                    'en_message' => 'your reservation appointment has been changed',
                    'ar_message' => 'لقد تم تغيير ميعاد الحجز الخاص بك',
                    'url' => 'reservations',
                    'object_id' => $reservation->id,
                    'table' => 'reservations',
                ];

                try {
                    // create notification to be pushed to user notifying him that reservation status has been changes
                    $notification = (new NotificationRepository())->createNewNotification($notification_data);
                } catch (\Exception $e) {
                    DB::rollBack();
                    self::logErr($e);
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-do-this-action'));
                }

                if ($receiver->is_notification == 1) {
                    $tokens = (new TokenRepository())->getTokensByUserId($reservation->user_id);
                    if ($tokens) {
                        try {
                            $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->url, $notification);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            self::logErr($e);
                            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-do-this-action'));
                        }
                    }
                }
            }
        }
        // check if request day is holiday
        if ($this->reservationRepository->checkIfHoliday($request->day, $reservation) != false) {
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.we-do-not-work-on-holidays'));
        }
        DB::commit();
        if ($auth_user->role_id === self::ROLE_ASSISTANT) {
            return self::jsonResponse(true, self::CODE_OK, trans('lang.reservations-done-successfully'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.reservations-done-successfully'), '', $reservation);
    }

    /**
     * check if patient reserve with active doctor or not if not activate the doctor who reserve with him
     * @param $user
     * @param $clinic
     * @return \Illuminate\Http\JsonResponse|mixed|\stdClass
     */
    private function checkIfReserveWithActiveDoctor($user, $clinic)
    {
        // get doctor user
        $doctor_user_model = UserTrait::getUserById($clinic->created_by);
        // activate and deactivate an account
        $active_doctor = (new DoctorRepository())->deactivateCurrentDoctorAndActivateTheGivenDoctor($user, $doctor_user_model->account_id);

        if (!$active_doctor) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.can-not-active-doctor'));
        }
        return $active_doctor;
    }

    /**
     * @param $user_id
     * @param $clinic_id
     * @return bool
     */
    private function checkIfUserCanRservationInClinicOrNot($user_id, $clinic_id)
    {

        $is_patient_test = in_array($user_id, get_test_users('patient')->toArray(), true) ? true : false;

        $clinic = $this->reservationRepository->getClinicById($clinic_id);
        if (!$clinic) {
            $is_doctor_test = true;
        } else {
            $doctor = (new AuthRepository())->getUserByAccount($clinic->account_id, self::ROLE_DOCTOR);
            if ($doctor) {
                $is_doctor_test = in_array($doctor->id, get_test_users('doctor')->toArray(), true) ? true : false;
            } else {
                $is_doctor_test = true;
            }
        }
        return $is_patient_test !== $is_doctor_test; // true => can reserve, false => cant reserve
    }

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function addReservation(Request $request)
    {
        $user = auth()->guard('api')->user();

        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // check if user can reserve or not (according to test and live accounts)
        $can_reserve = $this->checkIfUserCanRservationInClinicOrNot($user->id, $request->get('clinic_id'));
        if ($can_reserve) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you-cant-reserve-here'));
        }

        // check if user is active or not
        if ($user->role_id == self::ROLE_USER && $user->is_active != self::ACTIVE) {
            return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.not-activated'), new \stdClass(), $user);
        }

        // Check if patient
        if (($user->role_id == self::ROLE_USER) && !self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        $user = auth()->guard('api')->user();

        if ($user->role_id == self::ROLE_USER && $request->has('offer_id')) {

            // check if he is using cash with offer or not
            if ($request->payment_method == self::METHOD_CASH) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you-cant-reserve-with-offer-and-cash'));
            }

            $offer = (new OfferRepository())->getOfferById($request->offer_id);
            if ($offer) {
                // first check offer expiration
                $offerExpiration = now()->diffInDays(Carbon::parse($offer->expiry_date), false);
                if ($offerExpiration <= 0) {
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.offer_expired'));
                }

                // second check if offer expiration is before reservation day
                $reservationExpiration = Carbon::parse($request->get('day'))->diffInDays(Carbon::parse($offer->expiry_date), false);
                if ($reservationExpiration < 0) {
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.offer_expired'));
                }
            }
        }

        // check promo-code existence
        if ($request->has('promo-code') && $request->get('promo-code') != '') {
            // check code validation and usage
            $isCodeValid = (new PromoCodeRepository())->checkCodeValidationAndUsage($user->id, $request['promo-code']);
            if (!$isCodeValid) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.code_invalid'));
            }
        }

        // to be deleted when change to reschedule method
        // assistant add reservation
        if ($user->role_id == self::ROLE_ASSISTANT) {
            // validate fields
            if (!$this->authValidationRepository->userIdValidation($request)) {
                return self::jsonResponse(false, self::CODE_VALIDATION, $this->authValidationRepository->getFirstError(), $this->authValidationRepository->getErrors());
            }
            // to add attributes to request
            $request['user_id'] = $request->user_id;
            $request->request->add(['clinic_id' => $user->clinic_id]);
            $request->request->add(['payment_method' => self::METHOD_CASH]);
            $request->request->add(['transaction_id' => -1]);

            $user = (new AuthRepository)->getUserById($request->user_id);
            if ($user === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-not-found'));
            }
        } // if assistant want to reschedule reservation
        elseif ($user->role_id == self::ROLE_ASSISTANT && $request->reservation_id) {
            // get reservation by id
            $reservation = $this->reservationRepository->getReservationById($request->reservation_id);
            if (!$reservation) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-not-found'));
            }
            // if assistant want to reschedule reservation in clinic she doesn't belong to
            if ($reservation->clinic_id != $user->clinic_id) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.this-assistant-not-related-to-this-clinic'));
            }
            // to set user same to reservation user
            $user = (new AuthRepository)->getUserById($reservation->user_id);
            if ($user === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.user-not-found'));
            }
            // to add attributes to request
            $request->request->add(['clinic_id' => $reservation->clinic_id]);
            $request->request->add(['payment_method' => $reservation->payment_method]);
            $request->request->add(['transaction_id' => $reservation->transaction_id]);
            $request->request->add(['complaint' => $reservation->complaint]);
            $request->request->add(['type' => $reservation->type]);
        }

        // validate fields
        if (!$this->reservationValidationRepository->addReservationValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        // get clinic
        $clinic = $this->reservationRepository->getClinicById($request->clinic_id);
        if (!$clinic) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.clinic-not-found'));
        }

        // search if user has reservation today and completed a visit => then don't accept any reservation for him
        $completed_visit_today = $this->reservationRepository->CheckPatientCompletedVisitToday($request->clinic_id, $user->id, $request->day);
        if ($completed_visit_today) {
            $reserve_today_msg = $user->role_id == self::ROLE_ASSISTANT ? trans('lang.assistant-you-can-not-reserve-today-again') : trans('lang.sorry-you-can-not-reserve-twice-in-same-day-with-same-doctor');
            return self::jsonResponse(false, self::CODE_FAILED, $reserve_today_msg);
        }

        // to check on transaction id
        if (in_array($request->payment_method, array(self::METHOD_ONLINE, self::METHOD_INSTALLMENT)) && ($request->transaction_id == "" || $request->transaction_id == null)) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.transaction-not-found'));
        }

        // ******************************************************************************** reschedule reservation  // ********************************************************************************
        // to be deleted when change to reschedule method
        if ($request->reservation_id) {
            $reschedule = $this->changeAppointment($request);
            if (!empty($reschedule)) {
                return $reschedule;
            }
        }

        // check if date not expired
        if (self::parseDate($request->day) < self::getToday()) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.day_expired'));
        }
        /*********  validate requested day if not in available days (in future)  ******/

        // check if doctor u need to reserve with is published
        $doctor_patient_want_to_reserve_with = (new AuthRepository)->getUserById($clinic->created_by);
        if ($doctor_patient_want_to_reserve_with === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.doctor_u_want_to_reserve_with_not_found'));
        }

        $check_if_doctor_still_available = (new DoctorRepository)->CheckIfDoctorPublished($doctor_patient_want_to_reserve_with->account_id);
        if (!$check_if_doctor_still_available) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.this_doctor_not_available_currently'));
        }

        // if user have reservation upcoming
        $upcoming_reservations = $this->reservationRepository->checkIfReservationUpcoming($user->id, 0, $request->clinic_id);
        if ($upcoming_reservations === false) {
            $assistant_response = self::jsonResponse(true, self::CODE_FAILED, trans('lang.assistant-one-upcoming-with-this-doctor'));
            $patient_response = self::jsonResponse(true, self::CODE_FAILED, trans('lang.two-upcoming-with-this-doctor'));
            $response = $user->role_id == self::ROLE_ASSISTANT ? $assistant_response : $patient_response;
            return $response;
        }
        // check if patient have reservation with this doctor today
        $patient_reservation_with_doctor = $this->reservationRepository->checkIfPatientReserveWithThisDoctorTwiceAtDayWhenAdd($request->clinic_id, $user->id, $request->day);
        if ($patient_reservation_with_doctor === true) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.sorry-you-can-not-reserve-twice-in-same-day-with-same-doctor'));
        }

        $approved_reservations_today = $this->reservationRepository->getApprovedReservationsCountToday($request->clinic_id);

        if ($approved_reservations_today === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }
        if ($approved_reservations_today >= $clinic->res_limit) {
            return self::jsonResponse(true, self::CODE_FAILED, trans('lang.all-appointments-reserved-check-later-for-availability'));
        }
        /****************************************************/
        // That means this clinic works with the times pattern
        /****************************************************/
        if ($clinic->pattern == self::PATTERN_INTERVAL) { // intervals
            return $this->addReservationWhenIntervalClinic($request, $clinic, $user);
        }

        // That means this clinic works with the queue pattern
        return $this->addReservationAndIncreaseQueue($request, $clinic, $user);
    }

    /**
     * reschedule reservation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function rescheduleReservation(Request $request)
    {
        $user = auth()->guard('api')->user();

        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // validate fields
        if (!$this->reservationValidationRepository->reservationIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }
        // get reservation by id
        $reservation = $this->reservationRepository->getReservationById($request->reservation_id);
        if (!$reservation) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-not-found'));
        }

        // if reservation not approved or missed can't reschedule it
        if ($reservation->status != self::STATUS_APPROVED && $reservation->status != self::STATUS_MISSED) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.status-not-valid'));
        }

        // get clinic
        $clinic = $this->reservationRepository->getClinicById($reservation->clinic_id);
        if (!$clinic) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.clinic-not-found'));
        }

        if ($clinic->pattern == self::PATTERN_INTERVAL) {
            // get working Hour
            $working_hour = $this->reservationRepository->getWorkingHourById($request->working_hour_id);
            if (!$working_hour) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.working-hours-not-found'));
            }
        }

        // check if request day is holiday
        if ($this->reservationRepository->checkIfHoliday($request->day, $reservation) != false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.we-do-not-work-on-holidays'));
        }

        // search if user has reservation today and completed a visit => then don't accept any reservation for him
        $completed_visit_today = $this->reservationRepository->CheckPatientCompletedVisitToday($reservation->clinic_id, $user->id, $request->day);
        if ($completed_visit_today) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.you-can-not-reserve-today-again'));
        }

        // check if doctor u need to reserve with is published and still due date available
        $doctor_patient_want_to_reserve_with = (new AuthRepository)->getUserById($clinic->created_by);
        if ($doctor_patient_want_to_reserve_with === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.doctor_u_want_to_reserve_with_not_found'));
        }

        $check_if_doctor_still_available = (new DoctorRepository)->CheckIfDoctorPublished($doctor_patient_want_to_reserve_with->account_id);
        if (!$check_if_doctor_still_available) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.this_doctor_not_available_currently'));
        }

        // check if patient have reservation with this doctor today
        $patient_reservation_with_doctor = $this->reservationRepository->checkIfPatientReserveWithThisDoctorTwiceAtDayWhenReschedule($reservation->clinic_id, $user->id, $request->day);
        if ($patient_reservation_with_doctor === true) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.sorry-you-can-not-reserve-twice-in-same-day-with-same-doctor'));
        }

        // if count of clinic is over
        $approved_reservations_today = $this->reservationRepository->getApprovedReservationsCountToday($reservation->clinic_id);

        if ($approved_reservations_today === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        if ($approved_reservations_today >= $clinic->res_limit) {
            return self::jsonResponse(true, self::CODE_FAILED, trans('lang.all-appointments-reserved-check-later-for-availability'));
        }

        // update reservation
        $update_reservation = $this->reservationRepository->editReservation($request, $user);
        if ($update_reservation === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        if ($user->role_id == self::ROLE_USER) {
            $active_doctor = $this->checkIfReserveWithActiveDoctor($user, $clinic);
        } else {
            $active_doctor = '';
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.reservation_updated_successfully'), "", $active_doctor);
    }

    /**
     * @param $request
     * @param $clinic
     * @param $user
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Exception
     */
    private function addReservationWhenIntervalClinic($request, $clinic, $user)
    {
        // get working Hour
        $working_hour = $this->reservationRepository->getWorkingHourById($request->working_hour_id);
        if (!$working_hour) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.working-hours-not-found'));
        }

        // check if working hour belong to this day and this clinic
        $check_working_hour_belong_to_clinic = $this->reservationRepository->checkWorkingHourBelongToClinic($request->working_hour_id, $clinic, $request->day);
        if (!$check_working_hour_belong_to_clinic) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.working-hours-not-belong-to-clinic'));
        }

        // check if working hour reserved
        $check_working_hour_reserved = $this->reservationRepository->checkIfWorkingHourReserved($request->working_hour_id, $request->day);
        if ($check_working_hour_reserved) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.working-hours-is-reserved'));
        }

        //if patient want to reserve in time passed
        $time_passed = $this->reservationRepository->checkTimePassed($request->day, $working_hour->time);
        if ($time_passed === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.time_expired'));
        }

        // create new reservation
        return $this->addReservationAndIncreaseQueue($request, $clinic, $user);
    }

    /**
     * @param $request
     * @param $clinic
     * @param $user
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Exception
     */
    private function addReservationAndIncreaseQueue($request, $clinic, $user)
    {
        // get largest queue to increase it in new reservation
        $largest_queue = $this->reservationRepository->getLargestQueue($request->clinic_id, $request->day);
        /*****************check if the reservation day has working hours or not ( in future if he send day not in our days)**************/
        if ($largest_queue) {
            // create new reservation and increase queue
            return $this->createUpdateReservationData($request, $clinic, $user, $largest_queue);
        }

        // create new reservation first one
        return $this->createUpdateReservationData($request, $clinic, $user);
    }

    /**
     * get reservation fees ( subtotal_fees, VAT , TotalFees)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservationFees(Request $request)
    {
        $user = auth()->guard('api')->user();

        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        // validate fields
        if (!$this->reservationValidationRepository->getClinicIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        $offer_id = null;
        // check the if there is offer or not and check expiration date
        if ($request->has('offer_id')) {
            // check offer expiration date
            $offer = (new OfferRepository())->getOfferById($request->offer_id);
            if ($offer) {
                $offer_id = $offer->id;
            } else {
                $offer_id = null;
            }
        }

        $reservation_fees = $this->reservationRepository->getReservationFees($request['services'] ?? null, $request->clinic_id, $request->type, $offer_id, $user);
        if ($reservation_fees === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        // check the promo-code and if valid change the payment
        if ($request->has('promo-code') && $request->get('promo-code') != -1) {
            $total_after_discount = $reservation_fees->total_fees;
            $promoCode = (new PromoCodeRepository())->getCodeValueAfterValidation($user->id, $request['promo-code']);
            if ($promoCode) {
                if ($promoCode->discount_type == 1) {
                    $total_after_discount -= ($total_after_discount * ($promoCode->discount / 100));
                    $discount = app()->getLocale() == 'en' ? $promoCode->discount . '% discount' : '%خصم ' . $promoCode->discount;
                } else {
                    $total_after_discount -= $promoCode->discount;
                    $discount = app()->getLocale() == 'en' ? $promoCode->discount . ' EGP discount' : 'خصم ' . $promoCode->discount . ' جنيه';
                }

                // responses appeded to response
                $reservation_fees->old_total = $reservation_fees->total_fees;  // old Total before Redeem
                $reservation_fees->total_fees = $total_after_discount;         // new Total after Redeem
                $reservation_fees->promo_msg = $discount;                   // discount message


                if ($total_after_discount < 0) {
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.code_invalid'));
                }

            } else {
                $reservation_fees->promo_msg = '';
                return self::jsonResponse(false, self::CODE_INVALID_PROMO, trans('lang.code_invalid'));
            }
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.get-reservation-fees'), [], $reservation_fees);
    }

    /**
     * reschedule time
     * @param Request $request
     * @return mixed
     */
    public function changeAppointment(Request $request)
    {
        // validate fields
        if (!$this->reservationValidationRepository->reservationIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        // get reservation by id
        $reservation = $this->reservationRepository->getReservationById($request->reservation_id);
        if (!$reservation) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-not-found'));
        }
        $clinic_queue = $this->reservationRepository->getClinicQueueToday($reservation);
        if ($clinic_queue) {
            if ($reservation->day == self::getDateByFormat(self::getToday(), 'Y-m-d') && $reservation->queue == $clinic_queue->queue && ($reservation->status == self::STATUS_APPROVED || $reservation->status == self::STATUS_ATTENDED)) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-reschedule-reservation-because-you-are-with-doctor'));
            }
        }

        // get clinic to know pattern
        $clinic = $this->reservationRepository->getClinicById($reservation->clinic_id);
        if (!$clinic) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.clinic-not-found'));
        }

        //get reservation status
        $status = $this->reservationRepository->getStatusName($reservation->status);

        // patient can't reserve if status not pending or approved
        if ($reservation->status == self::STATUS_CANCELED || $reservation->status == self::STATUS_ATTENDED) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-reschedule-reservation-status') . $status);
        }
        $reservation->delete();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function setReservationStatus(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if nit authorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // validate fields
        if (!$this->reservationValidationRepository->setStatusValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }
        // get reservation by id
        $reservation = $this->reservationRepository->getReservationById($request->reservation_id);
        if (!$reservation) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-not-found'));
        }
        DB::beginTransaction();

        if ($user->role_id == self::ROLE_ASSISTANT && $user->clinic_id == $reservation->clinic_id) {

            // because assistant app in mobile can cancel any reservation
            if ($request->status == self::STATUS_CANCELED) {

                if ($reservation->check_in != null && $reservation->check_out == null) {
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-delete-reservation-because-you-are-with-doctor'));
                }
                if ($reservation->check_in != null && $reservation->check_out != null && $reservation->status == self::STATUS_APPROVED) {
                    return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-delete-reservation-because-you-are-with-doctor'));
                }
                // set  status for reservations
                try {
                    $this->reservationRepository->changeReservationStatus($reservation, $request->status);
                } catch (\Exception $e) {
                    self::logErr($e->getMessage());
                    DB::rollBack();
                }

                $ar_msg = 'لقد تم الغاء الحجز الخاص بك';
                $en_smg = 'your reservation has been canceled';

                // send notification to user
                $notification_data = [
                    'multicast' => 0, //0 => for user, any other number will represent user roles
                    'sender_id' => $user->id,
                    'receiver_id' => $reservation->user_id,
                    'en_title' => $user->account['en_name'],
                    'ar_title' => $user->account['ar_name'],
                    'en_message' => $en_smg,
                    'ar_message' => $ar_msg,
                    'url' => 'reservations',
                    'object_id' => $reservation->id,
                    'table' => 'reservations',
                ];

                try {
                    // create notification to be pushed to user notifying him that reservation status has been changes
                    $notification = (new NotificationRepository())->createNewNotification($notification_data);
                } catch (\Exception $e) {
                    DB::rollBack();
                    self::logErr($e);
                    return self::jsonResponse(true, self::CODE_FAILED, trans('lang.notifications_failed'));

                }
                if (!$notification) {
                    return self::jsonResponse(true, self::CODE_FAILED, trans('lang.notifications_failed'));
                }

                // send message to user
                try {
                    self::sendRklinicSmsMessage($reservation->user->mobile, $reservation->user->lang == 'en' ? $en_smg : $ar_msg);
                } catch (\Exception $e) {
                    DB::rollBack();
                    self::logErr($e);
                    return self::jsonResponse(true, self::CODE_FAILED, trans('lang.failed-send-sms'));

                }

                $tokens = (new TokenRepository())->getTokensByUserId($notification->receiver_id);
                if ($tokens) {
                    $this->push_notification($notification[app()->getLocale() . '_title'], $notification[app()->getLocale() . '_message'], $tokens, $notification->url, $notification);
                }
                DB::commit();

                return self::jsonResponse(true, self::CODE_OK, trans('lang.reservation-cancel'));
            } else {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-status'));
            }
        } // if patient want to cancel reservation
        elseif ($user->role_id == self::ROLE_USER && $request->status == self::STATUS_CANCELED && $reservation->user_id == $user->id) {
            // get clinic to know pattern
            $clinic = $this->reservationRepository->getClinicById($reservation->clinic_id);
            if (!$clinic) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.clinic-not-found'));
            }
            //get reservation status
            $status = $this->reservationRepository->getStatusName($reservation->status);
            if ($reservation->status == self::STATUS_CANCELED || $reservation->status == self::STATUS_ATTENDED || $reservation->status == self::STATUS_MISSED) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-reschedule-reservation-status') . $status);
            }
            if ($reservation->check_in != null && $reservation->check_out != null && $reservation->status == self::STATUS_APPROVED) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-delete-reservation-because-you-are-with-doctor'));
            }
            if ($reservation->check_in != null && $reservation->check_out == null) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-delete-reservation-because-you-are-with-doctor'));
            }
            try {
                $this->reservationRepository->changeReservationStatus($reservation, $request->status);
            } catch (\Exception $e) {
                self::logErr($e->getMessage());
                DB::rollBack();
            }

            $account = $clinic->account;
            $request['object_id'] = $reservation->id;

            if ($account->type == self::ACCOUNT_TYPE_SINGLE) {
                // single doctor
                $request['multicast'] = 2;
                $request['receiver_id'] = $clinic->id;
                $request['en_message'] = $user->name . ' has canceled reservation in clinic' . $clinic->en_name;
                $request['ar_message'] = ' لقد قام ' . $user->name . ' بالغاء حجزه فى عياده ' . $clinic->ar_name;
                $request['url'] = '/reservations/all?notification=';
            } else {
                // poly-clinic
                $request['multicast'] = 1;
                $request['receiver_id'] = (new AuthRepository())->getUserByAccount($account->id, self::ROLE_DOCTOR)->id;
                $request['en_message'] = $user->name . ' has canceled reservation in clinic ' . $clinic->en_name;
                $request['ar_message'] = ' لقد قام ' . $user->name . ' بالغاء حجزه فى عياده ' . $clinic->ar_name;
                $request['url'] = '/reservations/all?' . '&clinic=' . $clinic->id . '&notification=';
            }

            // send notification
            $request['sender_id'] = $user->id;
            $request['en_title'] = $clinic->account['en_name'];
            $request['ar_title'] = $clinic->account['ar_name'];
            $request['table'] = 'reservations';
            try {
                (new NotificationController($request))->pushAdminNotification($request);
            } catch (\Exception $e) {
                self::logErr($e->getMessage());
                DB::rollBack();
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-do-this-action'));
            }
            DB::commit();
            return self::jsonResponse(true, self::CODE_OK, trans('lang.reservations-canceled'));
        }
        return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-do-this-action'));
    }

    /**
     * get number of patients
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getPatientsCount($upcoming_reservation)
    {
        $patients_count_and_clinic_start_time = new \stdClass();

        //when queue doesn't start yet
        //get all reservation before me if clinic work intervals and it's time smaller than may time
        try {
            $patients_approved_count = $this->reservationRepository->getPatientsCount($upcoming_reservation);
        } catch (\Exception $e) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }
        //  get index day from days list in config
        try {
            $index = self::getDayIndex($upcoming_reservation->day);
        } catch (\Exception $e) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        // get clinic start time
        $working_hour = $this->reservationRepository->getClinicStartTime($upcoming_reservation->clinic_id, $index, $upcoming_reservation->day);

        if ($working_hour === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.working-hours-not-found'));
        }
        $patients_count_and_clinic_start_time->patients_approved_count = $patients_approved_count;
        $patients_count_and_clinic_start_time->clinic_start_time = $working_hour->time;
        return $patients_count_and_clinic_start_time;
    }

    /**
     * get upcoming reservation  estimated time
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getUpcomingReservationEstimatedTime($upcoming_reservation)
    {
        //call getPatientsCount to get patients count before me and clinic start time
        $patients_count_and_clinic_start_time = $this->getPatientsCount($upcoming_reservation);
        if ($patients_count_and_clinic_start_time === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        //get all attended reservation before me if clinic work intervals
        $patients_attended_and_first_reservation = $this->reservationRepository->getAttendedReservationAndFirstReservation($upcoming_reservation);
        if ($patients_attended_and_first_reservation === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
        }

        // get attended patients and there count and first reservation
        $patients_attended_count = $patients_attended_and_first_reservation->patients_attended_count;
        $patients_attended = $patients_attended_and_first_reservation->patients_attended;
        $first_reservation = $patients_attended_and_first_reservation->first_reservation;

        // get estimated time after started
        // get avg visit time and divided it on count of attended patients
        if ($patients_attended_count >= 1) {
            // get estimated time after started
            $estimated_time = $this->reservationRepository->getUpcomingReservationEstimatedTimeAfterClinicStart($patients_attended, $first_reservation->check_in,
                $patients_count_and_clinic_start_time->patients_approved_count, $upcoming_reservation);
            if ($estimated_time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }
        } else {
            // get estimated time before started
            // if no attended patients ahead of me so i am the first so time will be clinic start time
            $estimated_time = $this->reservationRepository->getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_count_and_clinic_start_time->patients_approved_count,
                $patients_count_and_clinic_start_time->clinic_start_time, $upcoming_reservation);
            if ($estimated_time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }
        }
        return $estimated_time;
    }

    /**
     * get upcoming reservation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingReservation()
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        // if user have reservation upcoming
        $upcoming_reservations = $this->reservationRepository->checkIfReservationUpcoming($user->id);

        if (count($upcoming_reservations) < 0) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no-upcoming-reservation'));
        }

        foreach ($upcoming_reservations as $key => $upcoming_reservation) {
            // don't allow add reservations if queue has been started
            $queue = $this->reservationRepository->getQueueToday($upcoming_reservation->clinic_id);
            // get clinic queue status

            if ($queue) {
                $upcoming_reservation->queue_status = $queue->queue_status;
            } else {
                $upcoming_reservation->queue_status = 0;
            }
            // dont close queue if reservation is not in the same day
            if ($upcoming_reservation->day != now()->format('Y-m-d')) {
                $upcoming_reservation->queue_status = 0;
            }
            // we now serve number , we check if reservation today and queue start today
            $upcoming_reservation->serving_number = ($queue && $upcoming_reservation->day == self::getDateByFormat(self::getToday(), 'Y-m-d')) ? $queue->queue : 0;

            // get upcoming reservation clinic
            $upcoming_reservation_clinic = $this->reservationRepository->getClinicById($upcoming_reservation->clinic_id);
            if (!$upcoming_reservation_clinic) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-get-reservation-clinic'));
            }

            $account_type = self::getAccountById($upcoming_reservation_clinic->account_id);
            if (!$account_type) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-get-account'));
            }

            //call getPatientsCount to get patients count before me and clinic start time
            $patients_count_and_clinic_start_time = $this->getPatientsCount($upcoming_reservation);
            if ($patients_count_and_clinic_start_time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $time = $this->reservationRepository->getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_count_and_clinic_start_time->patients_approved_count,
                $patients_count_and_clinic_start_time->clinic_start_time, $upcoming_reservation);
            if ($time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $estimated_time = $this->getUpcomingReservationEstimatedTime($upcoming_reservation);
            if ($estimated_time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $upcoming_reservation->number = $upcoming_reservation->queue;
            $upcoming_reservation->time = $time;
            $upcoming_reservation->estimated_time = ($account_type->type == self::ACCOUNT_TYPE_POLY) ? $time : $estimated_time;

            // get formatted date, time, estimated time
            $formatted = $this->reservationRepository->getUpcomingReservationFormatted($upcoming_reservation->status, $upcoming_reservation->day, $upcoming_reservation->time, $upcoming_reservation->estimated_time);
            if ($formatted === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $reservation_day = $upcoming_reservation->day;
            $reservation_clinic_id = $upcoming_reservation->clinic_id;
            $dayIndex = self::getDayIndex($upcoming_reservation->day);
            // to customize upcoming reservation
            $upcoming_reservation = $this->reservationRepository->setUpcomingReservationData($upcoming_reservation, $formatted);

            $upcoming_reservation->clinic_id = $upcoming_reservation_clinic->id;
            // change clinic name in case of poly clinic
            if ($upcoming_reservation_clinic->account->type == self::ACCOUNT_TYPE_POLY) {
                $upcoming_reservation->clinic_name = $upcoming_reservation_clinic[app()->getLocale() . '_name'];
            }

            $upcoming_reservation->clinic = (new ClinicRepository())->getClinicInfo($upcoming_reservation_clinic->id, $account_type->type);

            $upcoming_reservation->account_type = $account_type->type;
            $upcoming_reservation->if_is_missed_msg = trans('lang.schedule-visit');
            // add reservation Details
            $upcoming_reservation->fees = $this->reservationRepository->getReservationFeesAfterReservation($upcoming_reservation->id);

            $clinic = (new ClinicRepository())->getClinicById($reservation_clinic_id);
            $doctor = User::join('accounts', 'accounts.id', 'users.account_id')->where('users.account_id', $clinic->account_id)->where('users.role_id', 1)->select('users.id', 'users.image', 'users.unique_id', 'users.name', 'accounts.type')->first();
            $upcoming_reservation->doctor = $doctor;
            $upcoming_reservation->full_day = $reservation_day;
            if ($doctor->type == 0) {
                $upcoming_reservation->address = $clinic->{app()->getLocale() . '_address'} ?? trans('lang.not_set');
            } else {
                $upcoming_reservation->address = $clinic->{app()->getLocale() . '_name'} ?? trans('lang.not_set');
            }

            // Get Clinic Data
            $upcoming_reservation->clinic_mobile = $upcoming_reservation_clinic->mobile;
            $upcoming_reservation->clinic_pattern = $upcoming_reservation_clinic->pattern;

            $min_max_of_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($upcoming_reservation_clinic->id, $dayIndex, $upcoming_reservation->day);
            if ($min_max_of_workingHours) {
                $upcoming_reservation->clinic_start = self::getDateByFormat($min_max_of_workingHours->min_time, 'g:i');
                $upcoming_reservation->clinic_start_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->min_time, 'A'));
                $upcoming_reservation->clinic_end = self::getDateByFormat($min_max_of_workingHours->max_time, 'g:i');
                $upcoming_reservation->clinic_end_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->max_time, 'A'));
            } else {
                $upcoming_reservation->clinic_start = null;
                $upcoming_reservation->clinic_start_range = null;
                $upcoming_reservation->clinic_end = null;
                $upcoming_reservation->clinic_end_range = null;
            }

            if ($clinic->pattern == ApiController::PATTERN_QUEUE) {
                // get break times
                $upcoming_reservation->clinic_break = (new WorkingHourRepository())->getArrayOfBreakTimesInQueue($clinic->id, $dayIndex);
            }

            $upcoming_reservations[$key] = $upcoming_reservation;
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.reservation-details'), [], $upcoming_reservations);
    }

    /**
     * get upcoming reservation details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upcomingReservationDetails(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // validate fields
        if (!$this->reservationValidationRepository->reservationIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        $upcoming_reservation_details = $this->reservationRepository->getUpcomingReservationDetails($request->reservation_id, $user, $this);
        if (!$upcoming_reservation_details) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-not-found'));
        }
        // get the reservation details
        return self::jsonResponse(true, self::CODE_OK, trans('lang.upcoming-reservation-details'), [], $upcoming_reservation_details);
    }


    /**
     * get past reservation details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pastReservationDetails(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // validate fields
        if (!$this->reservationValidationRepository->reservationIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        $upcoming_reservation_details = $this->reservationRepository->getPastReservationDetails($request->reservation_id, $user, $this);
        if (!$upcoming_reservation_details) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservation-not-found'));
        }
        // get the reservation details
        return self::jsonResponse(true, self::CODE_OK, trans('lang.upcoming-reservation-details'), [], $upcoming_reservation_details);
    }

    /**
     * get past reservation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPastReservation(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;
        // if user have reservation upcoming
        $past_reservations = $this->reservationRepository->checkIfReservationPast($user->id, $offset, $limit);

        if (count($past_reservations) < 0) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no-upcoming-reservation'));
        }

        foreach ($past_reservations as $key => $past_reservation) {

            // get upcoming reservation clinic
            $past_reservation_clinic = $this->reservationRepository->getClinicById($past_reservation->clinic_id);
            if (!$past_reservation_clinic) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-get-reservation-clinic'));
            }

            $account_type = self::getAccountById($past_reservation_clinic->account_id);
            if (!$account_type) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed-get-account'));
            }

            //call getPatientsCount to get patients count before me and clinic start time
            $patients_count_and_clinic_start_time = $this->getPatientsCount($past_reservation);
            if ($patients_count_and_clinic_start_time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $time = $this->reservationRepository->getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_count_and_clinic_start_time->patients_approved_count,
                $patients_count_and_clinic_start_time->clinic_start_time, $past_reservation);
            if ($time === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $past_reservation->number = $past_reservation->queue;
            $past_reservation->time = $time;

            // get formatted date, time, estimated time
            $formatted = $this->reservationRepository->getPastReservationFormatted($past_reservation->day, $past_reservation->time);
            if ($formatted === false) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something-wrong'));
            }

            $reservation_day = $past_reservation->day;
            $reservation_clinic_id = $past_reservation->clinic_id;


            $dayIndex = self::getDayIndex($past_reservation->day);
            // to customize upcoming reservation
            $past_reservation = $this->reservationRepository->setUpcomingReservationData($past_reservation, $formatted);

            $past_reservation->clinic = (new ClinicRepository())->getClinicInfo($past_reservation_clinic->id, $account_type->type);

            // check the review of the reservation
            $review = (new ReviewRepository())->checkReservationReview($past_reservation->id);
            $past_reservation->has_review = $review ? 1 : 0;
            $past_reservation->rate = $review->rate ?? 0;
            $past_reservation->clinic_id = $past_reservation_clinic->id;
            // change clinic name in case of poly clinic
            if ($past_reservation_clinic->account->type == self::ACCOUNT_TYPE_POLY) {
                $past_reservation->clinic_name = $past_reservation_clinic[app()->getLocale() . '_name'];
            }

            $past_reservation->account_type = $account_type->type;
            $past_reservation->if_is_missed_msg = trans('lang.schedule-visit');

            // add reservation Details
            $past_reservation->fees = $this->reservationRepository->getReservationFeesAfterReservation($past_reservation->id) ?? new \stdClass();

            // get reservation cashback in case of past Reservation
            $patient_cashBack = DB::table('cash_back')->where('reservation_id', $past_reservation->id)->first();
            $past_reservation->fees->cashback = $patient_cashBack->patient_cash ?? 0;

            $clinic = (new ClinicRepository())->getClinicById($reservation_clinic_id);
            $doctor = User::join('accounts', 'accounts.id', 'users.account_id')
                ->where('users.account_id', $clinic->account_id)
                ->where('users.role_id', 1)
                ->select('users.id', 'users.image', 'users.unique_id', 'users.name', 'accounts.type')
                ->first();
            $past_reservation->doctor = $doctor;
            $past_reservation->full_day = $reservation_day;
            if ($doctor->type == 0) {
                $past_reservation->address = $clinic->{app()->getLocale() . '_address'} ?? trans('lang.not_set');
            } else {
                $past_reservation->address = $clinic->{app()->getLocale() . '_name'} ?? trans('lang.not_set');
            }

            // Get Clinic Data
            $past_reservation->clinic_mobile = $past_reservation_clinic->mobile;
            $past_reservation->clinic_pattern = $past_reservation_clinic->pattern;

            $min_max_of_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($past_reservation_clinic->id, $dayIndex, $past_reservation->day);
            if ($min_max_of_workingHours) {
                $past_reservation->clinic_start = self::getDateByFormat($min_max_of_workingHours->min_time, 'g:i');
                $past_reservation->clinic_start_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->min_time, 'A'));
                $past_reservation->clinic_end = self::getDateByFormat($min_max_of_workingHours->max_time, 'g:i');
                $past_reservation->clinic_end_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->max_time, 'A'));
            } else {
                $past_reservation->clinic_start = null;
                $past_reservation->clinic_start_range = null;
                $past_reservation->clinic_end = null;
                $past_reservation->clinic_end_range = null;
            }

            $past_reservation->can_refund = false;

            if ($past_reservation->payment_method !== self::METHOD_CASH && in_array($past_reservation->status, [self::STATUS_MISSED, self::STATUS_APPROVED, self::STATUS_CANCELED], true)) {
                $previous_refund = DB::table('refund')->where('reservation_id', $past_reservation->id)->first();
                if (!$previous_refund) {
                    $past_reservation->can_refund = true;
                }
            }

            $past_reservations[$key] = $past_reservation;
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.reservation-details'), [], $past_reservations);
    }

    /**
     *  validate transaction code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setOnlineReservationPaid(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        // Check if patient
        if (self::checkIfDoctor($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, $validator->errors()->first(), $validator->errors());
        }

        $reservation = $this->reservationRepository->getReservationById($request['reservation_id']);

        if (!$reservation) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.reservations-not-found'));
        }


        if (!isset($request['transaction_id'])) {
            $request['transaction_id'] = '!~#%$^&*(&^';
        }

        if ($reservation->payment_method == self::METHOD_CASH ||
            ($reservation->payment_method == self::METHOD_INSTALLMENT && $request['transaction_id'] == $reservation->transaction_id)) {
            try {
                $this->reservationRepository->changeReservationStatus($reservation, self::STATUS_ATTENDED);
            } catch (\Exception $e) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.unknown_error'));
            }
        } else if ($reservation->payment_method == self::METHOD_ONLINE && $request['transaction_id'] == $reservation->transaction_id) {
            try {
                $this->reservationRepository->changeReservationStatus($reservation, self::STATUS_ATTENDED);
            } catch (\Exception $e) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.unknown_error'));
            }
            // check if there is cashBack then give the user it's share of cashBack (in case of online payment only)
            $patient_cashBack = DB::table('cash_back')->where('reservation_id', $reservation->id)->first();
            if ($patient_cashBack) {
                // Yes There Is CashBack
                $patient = (new AuthRepository())->getUserById($reservation->user_id);
                // Add CashBack Value To Patient
                (new AuthRepository())->updateColumn($patient, 'cash_back', $patient_cashBack->patient_cash);
            }
        } else {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-transaction'));
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.payment_confirmed'));
    }
}
