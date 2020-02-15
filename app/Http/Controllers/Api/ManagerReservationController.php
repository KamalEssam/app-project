<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\ClinicRepository;
use App\Http\Repositories\Api\ReservationRepository;
use App\Http\Repositories\Validation\ReservationValidationRepository;
use App\Http\Repositories\Web\ClinicQueueRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\StandByRepository;
use App\Http\Repositories\Web\VisitRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\UserTrait;
use DB;
use Illuminate\Http\Request;

class ManagerReservationController extends ApiController
{
    private $reservationRepository, $reservationValidationRepository;
    use UserTrait, DateTrait;

    /**
     * ReservationController constructor.
     * @param Request $request
     * @param ReservationRepository $reservationRepository
     * @param ReservationValidationRepository $reservationValidationRepository
     */
    public function __construct(Request $request, ReservationRepository $reservationRepository, ReservationValidationRepository $reservationValidationRepository)
    {
        $this->reservationRepository = $reservationRepository;
        $this->reservationValidationRepository = $reservationValidationRepository;
        $this->setLang($request);
    }


    public function getUpcomingReservations(Request $request)
    {
        $user = auth()->guard('api')->user();
        $doctor = (new AuthRepository)->getUserByAccount($user->account_id, ApiController::ROLE_DOCTOR);
        if (!$doctor) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.doctor-not-found'), new \stdClass(), []);
        }
        // Check if doctor or assistant
        if (!self::checkIfDoctorOrAssistant($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.not-valid-to-login-here'), new \stdClass(), []);
        }
        $clinics_related_to_account = (new ClinicRepository())->getClinicsRelatedToSameAccount($user->account_id);
        if ($clinics_related_to_account == false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'), new \stdClass(), []);
        }
        if ($clinics_related_to_account->count() <= 0) {
            return self::jsonResponse(true, self::CODE_NO_CLINICS, trans('lang.doctor-clinics-empty'), new \stdClass(), []);
        }
        $upcoming_days_with_reservations = $this->reservationRepository->addReservationsToSpecificDay($request, $doctor);
        if ($upcoming_days_with_reservations === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'), new \stdClass(), []);
        }
        // to send array if empty
        $upcoming_days_with_reservations = (empty($upcoming_days_with_reservations) || $upcoming_days_with_reservations == null) ? [] : $upcoming_days_with_reservations;
        return self::jsonResponse(true, self::CODE_OK, trans('lang.upcoming-reservations'), new \stdClass(), $upcoming_days_with_reservations);
    }


    /********************************
     ********************************
     ***                          ***
     ***      Doctor App          ***
     ***                          ***
     ********************************
     ********************************/

    /**
     *  start the Queue
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function startQueue(Request $request)
    {
        $auth_user = auth()->guard('api')->user();
        if (self::checkIfAssistant($auth_user->id) === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }
        $clinicQueue = new ClinicQueueRepository();       // new instance from clinic Queue
        $reservation = new \App\Http\Repositories\Web\ReservationRepository(); // new instance from Reservation web Repository
        $account = self::getAccountById($auth_user->account_id);
        if (!$account) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.account_not_found'), [], new \stdClass());
        }

        // get clinic belongs to assistant
        $clinic = (new ClinicRepository())->getClinicById($auth_user->clinic_id);
        if (!$clinic) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.clinic_not_found'), [], new \stdClass());
        }
        // get clinic queue
        $queue = $clinicQueue->getClinicQueueByClinic($clinic->id);
        // if clinic hasn't have queue  get first reservation in this day and set clinic queue = reservation queue
        if (!$queue) {
            // this part will fetch the first reservation which approved
            $today_first_reservation = $reservation->getNextReservationInQueue(self::STATUS_APPROVED, $clinic->id, 0);
            if (!$today_first_reservation) {
                return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.no_reservations'), [], new \stdClass());
            }
            DB::beginTransaction();
            try {
                $queue = $clinicQueue->createQueue($clinic->id, $today_first_reservation->queue);
            } catch (\Exception $e) {
                $this->logErr($e->getMessage());
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.failed_to_start_queue'), [], new \stdClass());
            }
            DB::commit();
        }

        $the_reservation = $reservation->getReservationByStatusAndClinic('', $clinic->id, $queue->queue);
        $the_reservation->clinic_name = $account[app()->getLocale() . '_name'];
        $user = (new AuthRepository())->getUserById($the_reservation->user_id);
        $the_reservation->name = $user->name;
        $the_reservation->image = $user->image;
        $the_reservation->unique_id = $user->unique_id;
        $the_reservation->address = $clinic[app()->getLocale() . '_address'];


        return $this->getReservationToManger($the_reservation, $queue);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function nextQueue(Request $request)
    {
        // validate fields to status field
        if (!$this->reservationValidationRepository->nextQueueValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        $auth_user = auth()->guard('api')->user();
        if (self::checkIfAssistant($auth_user->id) === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }
        // get clinic belongs to assistant
        $clinic = (new ClinicRepository())->getClinicById($auth_user->clinic_id);
        if (!$clinic) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.clinic_not_found'), [], new \stdClass());
        }
        $clinicQueue = new ClinicQueueRepository();       // new instance from clinic Queue
        $reservationRepo = new \App\Http\Repositories\Web\ReservationRepository(); // new instance from Reservation web Repository
        $account = self::getAccountById($auth_user->account_id);
        if (!$account) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.account_not_found'), [], new \stdClass());
        }
        // get clinic queue
        $queue = $clinicQueue->getClinicQueueByClinic($clinic->id);
        if (!$queue) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.queue_not_started'), [], new \stdClass());
        }
        // get the previous reservation
        $previous_reservation = $reservationRepo->getReservationByStatusAndClinic([self::STATUS_APPROVED, self::STATUS_ATTENDED], $clinic->id, $queue->queue);
        if ($previous_reservation) {
            $doctor_settings = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId();

            // means restrict visit
            if ($doctor_settings->restrict_visit == 1) {
                if ($request->status == self::STATUS_MISSED && in_array($previous_reservation->status, [self::STATUS_ATTENDED, self::STATUS_MISSED])) {
                    return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.visit_already_ended'), [], new \stdClass());
                }
            } else {
                $visit = (new VisitRepository())->getVisitByReservationId($previous_reservation->id);
                // in case the reservation is attended
                if ($request->status == self::STATUS_ATTENDED && !$visit) {
                    return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.no_visit_added'), [], new \stdClass());
                    // in case of missed reservation
                } elseif ($request->status == self::STATUS_MISSED && $visit) {
                    return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.sorry-doctor-add-visit'), [], new \stdClass());
                }
            }

            // update the previous reservation to the given status in request and set checkout date
            $reservationRepo->ChangeReservationStatusAfterVisit($previous_reservation, $request->status, $auth_user->id);
        }
        // check stand By First to get Reservation Else Get the next reservation
        $standBy = (new StandByRepository())->getStandBy($auth_user->clinic_id);
        if ($standBy) {
            DB::beginTransaction();
            // there is standBy Record
            try {
                // get it's reservation
                $reservation = $reservationRepo->getReservationById($standBy->reservation_id);
                // delete the stand By column
                (new StandByRepository())->deleteStandBy($standBy);
            } catch (\Exception $e) {
                DB::rollBack();
                // update next reservation status
                $reservation = $reservationRepo->getNextReservationInQueue(self::STATUS_APPROVED, $clinic->id, $queue->queue);
            }

            DB::commit();
        } else {
            // update next reservation status
            $reservation = $reservationRepo->getNextReservationInQueue(self::STATUS_APPROVED, $clinic->id, $queue->queue);
        }

        // in case there is no next reservations
        if (is_null($reservation)) {
            return self::jsonResponse(false, self::CODE_METHOD_NOT_ALLOWED, trans('lang.no-reservation-yet'), [], new \stdClass());
        }

        // update queue set to next reservation queue
        $reservation = $reservationRepo->addReservationCheckInAndOut($reservation, $auth_user->id);
        $clinicQueue->setQueueToNextReservation($queue, $reservation->queue);

        // make the reservation Ready
        $reservation->clinic_name = $account[app()->getLocale() . '_name'];
        $user = (new AuthRepository())->getUserById($reservation->user_id);
        $reservation->name = $user->name;
        $reservation->image = $user->image;
        $reservation->unique_id = $user->unique_id;
        $reservation->address = $clinic[app()->getLocale() . '_address'];

        return $this->getReservationToManger($reservation, $queue);
    }

    /**
     * @param $upcoming_reservation
     * @return \Illuminate\Http\JsonResponse|\stdClass
     */
    private function getPatientsCount($upcoming_reservation)
    {
        $patients_count_and_clinic_start_time = new \stdClass();

        //when queue doesn't start yet
        //get all reservation before me if clinic work intervals and it's time smaller than may time
        try {
            $patients_approved_count = $this->reservationRepository->getPatientsCount($upcoming_reservation);
        } catch (\Exception $e) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        //  get index day from days list in config
        try {
            $index = self::getDayIndex($upcoming_reservation->day);
        } catch (\Exception $e) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
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
     *  get reservation data for manager
     *
     * @param $reservation
     * @param $queue
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservationToManger($reservation, $queue)
    {
        // if user have reservation upcoming
        $upcoming_reservation = $reservation;
        // we now serve number
        $upcoming_reservation->serving_number = $queue ? $queue->queue : 0;

        //call getPatientsCount to get patients count before me and clinic start time
        $patients_count_and_clinic_start_time = $this->getPatientsCount($upcoming_reservation);
        if ($patients_count_and_clinic_start_time === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        $time = $this->reservationRepository->getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_count_and_clinic_start_time->patients_approved_count,
            $patients_count_and_clinic_start_time->clinic_start_time, $upcoming_reservation);
        if ($time === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }
        $upcoming_reservation->number = $upcoming_reservation->queue;
        $upcoming_reservation->time = $time;

        // get formatted date, time, estimated time
        $formatted = $this->reservationRepository->getUpcomingReservationFormatted($upcoming_reservation->status, $upcoming_reservation->day, $upcoming_reservation->time, $upcoming_reservation->estimated_time);
        if ($formatted === false) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.something_wrong'));
        }

        // to customize upcoming reservation
        $upcoming_reservation = $this->reservationRepository->setReservationDataForManger($upcoming_reservation, $formatted);

        return self::jsonResponse(true, self::CODE_OK, trans('lang.reservation-details'), [], $upcoming_reservation);
    }

    /**
     *  Set Reservation As StandBy
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function setReservationAsStandBy(Request $request)
    {
        // validate fields reservation_id field
        if (!$this->reservationValidationRepository->reservationIdValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
        }

        // check if user is not assistant
        $user = auth()->guard('api')->user();
        if ($user->role_id != self::ROLE_ASSISTANT) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        // get the reservation
        $reservation = $this->reservationRepository->getReservationById($request->reservation_id);
        if (!$reservation) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.reservation-not-found'), [], new \stdClass());
        }

        // check if reservation is missed or not
        if ($reservation->status != self::STATUS_MISSED) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.reservation-not-found'), [], new \stdClass());
        }

        // check if there is already standBy reservations or not
        $standBy = new StandByRepository();
        if ($standBy->getStandBy($reservation->clinic_id)) {
            return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.already-there-is-standBy'), [], new \stdClass());
        }

        // put the reservation in stand by
        DB::beginTransaction();

        // check if the reservation in queue missed or attended
        // then directly put the reservation as next in Queue
        // get clinic queue
        $clinicQueue = new ClinicQueueRepository();       // new instance from clinic Queue
        $queue = $clinicQueue->getClinicQueueByClinic($reservation->clinic_id);
        if (!$queue) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.queue_not_started'), [], new \stdClass());
        }
        $previous_reservation = (new \App\Http\Repositories\Web\ReservationRepository())->getReservationByStatusAndClinic('', $reservation->clinic_id, $queue->queue);
        if (!$previous_reservation) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_reservations'), [], new \stdClass());
        }
        // in case the current reservation is missed or attended
        if (in_array($previous_reservation->status, [self::STATUS_ATTENDED, self::STATUS_MISSED])) {
            // directly put the standby reservation in queue
            // first change the status
            $this->reservationRepository->changeReservationStatus($reservation, self::STATUS_APPROVED);
            // the put it in the queue
            $reservation = (new \App\Http\Repositories\Web\ReservationRepository())->addReservationCheckInAndOut($reservation, $user->id);
            $clinicQueue->setQueueToNextReservation($queue, $reservation->queue);

        } else {
            try {
                $standBy->setStandBy($reservation->id, $reservation->queue, $reservation->clinic_id);
                $this->reservationRepository->changeReservationStatus($reservation, self::STATUS_APPROVED);
            } catch (\Exception $e) {
                DB::rollBack();
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.standBy'), [], new \stdClass());
            }
        }
        // change the status to attended
        // return success message
        DB::commit();
        return self::jsonResponse(true, self::CODE_OK, trans('lang.standBy_ok'), [], new \stdClass());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getCurrentReservation(Request $request)
    {
        $auth_user = auth()->guard('api')->user();

        $clinicQueue = new ClinicQueueRepository();       // new instance from clinic Queue
        $reservation = new \App\Http\Repositories\Web\ReservationRepository(); // new instance from Reservation web Repository
        $account = self::getAccountById($auth_user->account_id);
        if (!$account) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.account_not_found'), [], new \stdClass());
        }
        if ($auth_user->role_id == self::ROLE_DOCTOR) {
            // validate fields reservation_id field
            if (!$this->reservationValidationRepository->getClinicIdValidation($request)) {
                return self::jsonResponse(false, self::CODE_VALIDATION, $this->reservationValidationRepository->getFirstError(), $this->reservationValidationRepository->getErrors());
            }

            $clinic = (new ClinicRepository())->getClinicById($request['clinic_id']);
            if (!$clinic) {
                return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.clinic_not_found'), [], new \stdClass());
            }

        } else {
            // get clinic belongs to assistant
            $clinic = (new ClinicRepository())->getClinicById($auth_user->clinic_id);
            if (!$clinic) {
                return self::jsonResponse(false, self::CODE_NOT_ACTIVE, trans('lang.clinic_not_found'), [], new \stdClass());
            }
        }

        // check if there is confirmed patients or not
        $reservations_count = $reservation->getAllReservationsByStatusAndClinic(self::STATUS_APPROVED, $clinic->id);

        if (count($reservations_count) == 0) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_reservations'), [], new \stdClass());
        }

        $queue = $clinicQueue->getClinicQueueByClinic($clinic->id);
        if (!$queue) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.queue_not_started'), [], new \stdClass());
        }
        $the_reservation = $reservation->getReservationByStatusAndClinic('', $clinic->id, $queue->queue);
        if (!$the_reservation) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_reservations'), [], new \stdClass());
        }

        // check if current reservation is missed or attended
        // in case of that, get next reservation in queue
        if (in_array($the_reservation->status, [self::STATUS_ATTENDED, self::STATUS_MISSED])) {
            // get next reservation
            $next_reservation = (new \App\Http\Repositories\Web\ReservationRepository())->getNextReservationInQueue(self::STATUS_APPROVED, $clinic->id, $queue->queue);
            if ($next_reservation) {
                // put it in the queue and change the name of it
                $next_reservation = (new \App\Http\Repositories\Web\ReservationRepository())->addReservationCheckInAndOut($next_reservation, $auth_user->id);
                $clinicQueue->setQueueToNextReservation($queue, $next_reservation->queue);
                $the_reservation = $next_reservation;
            }
        }

        $the_reservation->clinic_name = $account[app()->getLocale() . '_name'];
        $user = (new AuthRepository())->getUserById($the_reservation->user_id);
        $the_reservation->name = $user->name;
        $the_reservation->image = $user->image;
        $the_reservation->unique_id = $user->unique_id;
        $the_reservation->address = $clinic[app()->getLocale() . '_address'];

        return $this->getReservationToManger($the_reservation, $queue);
    }
}
