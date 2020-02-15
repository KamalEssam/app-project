<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\ClinicQueueRepository;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\DoctorDetailsRepository;
use App\Http\Repositories\Web\ReservationRepository;
use App\Http\Repositories\Web\StandByRepository;
use App\Http\Repositories\Web\VisitRepository;
use DB;
use Illuminate\Http\Request;

class QueueController extends WebController
{
    private $reservation;
    private $clinicQueue;

    public function __construct(ReservationRepository $reservationRepository, ClinicQueueRepository $clinicQueueRepository)
    {
        $this->reservation = $reservationRepository;
        $this->clinicQueue = $clinicQueueRepository;
    }

    /**
     *  check if the queue is already started or not
     *  and if started get the current patient
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        $clinic = ClinicRepository::getClinicById(auth()->user()->clinic_id);

        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }

        // get current queue information
        $queue = $this->clinicQueue->getClinicQueueByClinic(auth()->user()->clinic_id);

        // if clinic doesn't  have queue  get first reservation in this day and set clinic queue = reservation queue
        $get_queue_number = $this->reservation->getReservationByStatusAndClinic(self::R_STATUS_APPROVED, $clinic->id, '', ['updated_at', 'desc']);
        $queue_number = 1;

        if ($queue) {
            $queue_number = $queue->queue;
        } elseif (!$queue && $get_queue_number) {
            $queue_number = $get_queue_number->queue;
        }
        // *********************************************************************************
        $reservation = $this->reservation->getReservationByStatusAndClinic([self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED, self::R_STATUS_MISSED], $clinic->id, $queue_number);
        if ($reservation) {
            $patient = AuthRepository::getUserByColumn('id', $reservation->user_id);
        } else {
            $patient = null;
        }

        return view('admin.assistant.queues.queue', compact('reservation', 'queue', 'patient'));
    }

    /**
     *  start the queue
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function startQueue()
    {
        $auth_user = auth()->user();
        // get clinic belongs to assistant
        $clinic = ClinicRepository::getClinicById($auth_user->clinic_id);

        // get clinic queue
        $queue = $this->clinicQueue->getClinicQueueByClinic($clinic->clinic_id);

        // if clinic hasn't have queue  get first reservation in this day and set clinic queue = reservation queue
        if (!$queue) {
            // this part will fetch the first reservation which approved
            $today_first_reservation = $this->reservation->getNextReservationInQueue(self::R_STATUS_APPROVED, $clinic->id, 0);

            if (!$today_first_reservation) {
                return response()->json('there is no reservations Today', 404);
            }

            DB::beginTransaction();
            try {
                $queue = $this->clinicQueue->createQueue($clinic->id, $today_first_reservation->queue);
            } catch (\Exception $e) {
                $this->logErr($e->getMessage());
                return response()->json('could not start queue', 404);
            }
            DB::commit();
            // get reservation in queue
            $reservation = $this->reservation->getReservationByStatusAndClinic(self::R_STATUS_APPROVED, $clinic->id, $queue->queue);

            if ($reservation) {
                $reservation = $this->reservation->addReservationCheckInAndOut($reservation, $auth_user->id);
                $patient = AuthRepository::getUserByColumn('id', $reservation->user_id);
            }

            return response()->json(view('admin.assistant.queues.queue-box', compact('reservation', 'queue', 'patient'))->render());

        }

    }


    /**
     *  change the status of the queue changeStatus
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function changeStatus(Request $request)
    {
        $queue = $this->clinicQueue->getClinicQueueByID($request->queue);
        // if clinic hasn't have queue  get first reservation in this day and set clinic queue = reservation queue
        if (!$queue) {
            return response()->json(['status' => false], 200);
        }

        try {
            (new ClinicQueueRepository())->updateQueue($queue, 'queue_status', $request['status']);
            return response()->json(['status' => true], 200);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return response()->json(['status' => false], 200);
        }
    }

    /**
     *  go to the next reservation in queue or put the previous reservation in case there is not next reservation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws mixed
     */
    public function nextQueue(Request $request)
    {
        $auth_user = auth()->user();
        // get clinic belongs to assistant
        $clinic = ClinicRepository::getClinicById($auth_user->clinic_id);
        // get clinic queue
        $queue = $this->clinicQueue->getClinicQueueByClinic($clinic->id);

        if ($queue->queue_status == -1) {
            return back();
        }

        $previous_reservation = $this->getPreviousReservation();

        if ($previous_reservation) {
            // get doctor details
            $doctor_settings = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId();

            if ($doctor_settings->restrict_visit == 1) {
                if ($request->status != self::R_STATUS_MISSED) {
                    $previous_reservation = $this->reservation->ChangeReservationStatusAfterVisit($previous_reservation, $request->status, $auth_user->id);
                }
            } else {
                $visit = (new VisitRepository())->getVisitByReservationId($previous_reservation->id);
                if ($visit && $request->status == self::R_STATUS_MISSED) {
                } else {
                    $previous_reservation = $this->reservation->ChangeReservationStatusAfterVisit($previous_reservation, $request->status, $auth_user->id);
                }
            }
        }

        // check stand By First to get Reservation Else Get the next reservation
        $standBy = (new StandByRepository())->getStandBy($auth_user->clinic_id);
        if ($standBy) {
            \DB::beginTransaction();
            // there is standBy Record
            try {
                // get it's reservation
                $reservation = $this->reservation->getReservationById($standBy->reservation_id);
                // delete the stand By column
                (new StandByRepository())->deleteStandBy($standBy);
            } catch (\Exception $e) {
                \DB::rollBack();
                // update next reservation status
                $reservation = $this->reservation->getNextReservationInQueue(self::R_STATUS_APPROVED, $clinic->id, $queue->queue);
            }

            \DB::commit();
        } else {
            // update next reservation status
            $reservation = $this->reservation->getNextReservationInQueue(self::R_STATUS_APPROVED, $clinic->id, $queue->queue);
        }
        if ($reservation) {
            // update queue set to next reservation queue
            $reservation = $this->reservation->addReservationCheckInAndOut($reservation, $auth_user->id);
            $queue = $this->clinicQueue->setQueueToNextReservation($queue, $reservation->queue);
            $patient = AuthRepository::getUserByColumn('id', $reservation->user_id);
        } else {
            // in case there is no next reservation set it to the last one
            $reservation = $previous_reservation;
        }

        return response()->json(view('admin.assistant.queues.queue-box', compact('reservation', 'queue', 'patient'))->render());
    }

    /**
     *  get the queue in Doctor status
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function doctorQueue()
    {
        if (!$_GET['clinic']) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }

        $clinic = ClinicRepository::getClinicById($_GET['clinic']);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        // get clinic queue
        $queue = $this->clinicQueue->getClinicQueueByClinic($clinic->id);

        if ($queue) {
            $reservation = $this->reservation->getReservationByStatusAndClinic([self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED], $clinic->id, $queue->queue);
            if ($reservation) {
                $patient = AuthRepository::getUserByColumn('id', $reservation->user_id);
            }
        } else {
            $patient = new \stdClass();
            $reservation = null;
            $queue = new \stdClass();
        }
        return view('admin.doctor.queues.current-queue', compact('patient', 'reservation', 'queue'));
    }


    /**
     *  get the previous reservation
     *
     * @return mixed
     * @throws \Exception
     */
    public function getPreviousReservation()
    {
        // get clinic belongs to assistant
        $clinic = ClinicRepository::getClinicById(auth()->user()->clinic_id);
        // get clinic queue
        $queue = $this->clinicQueue->getClinicQueueByClinic($clinic->id);
        // get reservation in queue
        if ($queue) {
            return $this->reservation->getReservationByStatusAndClinic([self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED], $clinic->id, $queue->queue);
        }
    }

    /**
     *  check if this reservation has Visit or Not
     *
     * @return string
     * @throws \Exception
     */
    public function checkVisit()
    {
        $doctor_settings = (new DoctorDetailsRepository())->getDoctorDetailsByAccountId();

        if ($doctor_settings->restrict_visit == 0) {
            // get previous reservation
            $previous_reservation = $this->getPreviousReservation();
            if ($previous_reservation) {
                $visit = (new VisitRepository())->getVisitByReservationId($previous_reservation->id);
                if (!$visit) {
                    return $check_visit = 'false';
                }
            }
        }
        return $check_visit = 'true';
    }
}
