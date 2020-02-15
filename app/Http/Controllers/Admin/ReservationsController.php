<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\ClinicQueueRepository;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\HolidayRepository;
use App\Http\Repositories\Web\NotificationRepository;
use App\Http\Repositories\Web\OfferRepository;
use App\Http\Repositories\Web\ReservationRepository;
use App\Http\Repositories\Web\StandByRepository;
use App\Http\Repositories\Web\TokenRepository;
use App\Http\Repositories\Web\WorkingHourRepository;
use App\Http\Traits\DateTrait;
use App\Http\Traits\NotificationTrait;
use App\Models\Clinic;
use App\Http\Requests\ReservationRequest;
use App\Models\ClinicQueue;
use App\Models\ReservationsPayment;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Config;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Validator;

class ReservationsController extends WebController
{
    use DateTrait, NotificationTrait;
    private $reservation;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservation = $reservationRepository;
    }

    /*
     * Desc : returns unread notifications for a specific user ..
     * Parameters :  { user_id : int, msg : string }
     * response : {}
     * /notifications/get-notification-indicator
     *
     * @param null $status
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /**
     * @param null $status
     * @return Factory|RedirectResponse|View
     */
    public function index($status = null)
    {
        // set missed reservation in previous day
        $this->reservation->reservation::setMissedReservation();

        $auth_user = auth()->user();

//        if ($auth_user->role_id == self::ROLE_DOCTOR) {
//            $clinic = ClinicRepository::getClinicById($_GET['clinic']);;
//        } else {
//            $clinic = ClinicRepository::getClinicById($auth_user->clinic_id);
//        }
        $clinic_id = (auth()->user()->role_id == self::ROLE_ASSISTANT) ? auth()->user()->clinic_id : ($_GET['clinic'] ?? null);
        //get the current clinic
        $clinic = ClinicRepository::getClinicById($clinic_id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        $clinicQueue = new ClinicQueueRepository();
        //check if assistant available to add reservation or not (don't allow if queue start)
        $queue = $clinicQueue->getClinicQueueByClinic($clinic_id);


        $reservations = $this->getReservationAccordingToStatus($status, $auth_user, Carbon::today('Africa/Cairo')->format('Y-m-d'), $clinic);
        if ($reservations == false) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.something_wrong'));
        }

        return view('admin.common.reservations.index', compact('reservations', 'status', 'clinic', 'queue'));
    }

    /**
     *  create new reservations
     *
     * @return Factory|RedirectResponse|View
     */
    public function create()
    {
        $clinic_id = (auth()->user()->role_id == self::ROLE_ASSISTANT) ? auth()->user()->clinic_id : ($_GET['clinic'] ?? null);
        //get the current clinic
        $clinic = ClinicRepository::getClinicById($clinic_id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        // get all mobiles for the patients in the system
        $getAllPatientsMobiles = (new AuthRepository())->getAllPatientsMobiles();

        if (!$getAllPatientsMobiles) {
            $getAllPatientsMobiles = [];
        }

        $results = implode('#', $getAllPatientsMobiles);
        return view('admin.common.reservations.create', compact('results', 'clinic'));
    }

    private function checkIfUserCanRservationInClinicOrNot($user_id, $clinic_id)
    {

        $is_patient_test = in_array($user_id, get_test_users('patient')->toArray(), true) ? true : false;

        $clinic = ClinicRepository::getClinicById($clinic_id);
        if (!$clinic) {
            $is_doctor_test = true;
        } else {
            $doctor = (new \App\Http\Repositories\Api\AuthRepository())->getUserByAccount($clinic->account_id, self::ROLE_DOCTOR);
            if ($doctor) {
                $is_doctor_test = in_array($doctor->id, get_test_users('doctor')->toArray(), true) ? true : false;
            } else {
                $is_doctor_test = true;
            }
        }
        return $is_patient_test !== $is_doctor_test; // true => can reserve, false => cant reserve
    }

    /**
     *  store reservation from assistant admin panel
     *
     * @param ReservationRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(ReservationRequest $request)
    {
        $auth_user = auth()->user();

        $user = (new AuthRepository())->getPatientUsingMobile($request['mobile']);
        if (!$user) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.user-not-found'));
        }
        // $clinic_id = (auth()->user()->role_id == self::ROLE_ASSISTANT) ? auth()->user()->clinic_id : (isset($_GET['clinic']) ? $_GET['clinic'] : null) ;
        $clinic_id = $request->clinic_id;

        // check the test users
        $can_reserve = $this->checkIfUserCanRservationInClinicOrNot($user->id, $clinic_id);
        if ($can_reserve) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.you-cant-reserve-here'));
        }

        //find clinic to get System
        $clinic = ClinicRepository::getClinicById($clinic_id);
        // check if patient has already reservation in that day
        if ($this->reservation->checkIfUserHasReservation($user->id, $request->day, $clinic->id)) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.already_have_reservation'));
        }

        $request['user_id'] = $user->id;
        $request['created_by'] = $auth_user->id;
        $request['status'] = self::R_STATUS_APPROVED;

        if ($request->working_hour_id != null) {
            $working_hour = (new WorkingHourRepository())->getWorkingHoursById($request->working_hour_id);
            if (is_object($working_hour)) {
                $expiry_date = $working_hour->expiry_date;
            } else {
                $expiry_date = null;
            }
        } else {
            $expiry_date = null;
        }

        DB::beginTransaction();
        if ($clinic->pattern == self::PATTERN_INTERVAL) {

            $working_hours = (new WorkingHourRepository())->getArrayOfWorkingHoursByClinicAndDay($clinic->id, self::getDayIndex($request->day), $expiry_date);
            try {
                $working_hour_for_reservation = (new WorkingHourRepository())->getWorkingHoursById($request->working_hour_id);
            } catch (Exception $e) {
                WebController::catchExceptions($e->getMessage());
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation_add_err'));
            }
            $request['queue'] = array_search($working_hour_for_reservation->time, $working_hours) + 1;
        } else {
            // check the number of reservation on that day
            $reservation_count = $this->reservation->getCountOfAllReservationsWhichIsApprovedAndAttended($clinic->id, $request->day);
            if ($reservation_count >= $clinic->res_limit) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no-appointments-in-that-day'));
            }

            // in case of Queue
            $reservation_queue = $this->reservation->getFirstReservationByClinicAndDayOrdered($clinic->id, $request->day);
            // in case there is people in the q
            if ($reservation_queue) {
                $largest_queue = $reservation_queue->queue;
                $request['queue'] = $largest_queue + 1;
            } else {
                // no reservations yet
                $request['queue'] = 1;
            }
        }

        $reservation = $this->reservation->createNewReservation($request);
        if (!$reservation) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation_add_err'));
        }

        // store the fees in the the database
        $payment = (new \App\Http\Repositories\Api\ReservationRepository())->getReservationFees(null, $clinic->id, $reservation->type, $reservation->offer_id ?? null, $user);
        if ($payment) {
            $doctor = User::where('account_id', $clinic->account_id)->where('role_id', self::ROLE_DOCTOR)->first();
            if (!$doctor) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.doctor-not-found'));
            }
            (new ReservationsPayment())->create([
                'reservation_id' => $reservation->id,
                'offer_id' => $reservation->offer_id ?? null,
                'fees' => ($reservation->type == self::TYPE_CHECK_UP) ? $clinic->fees : $clinic->follow_up_fees,
                'premium_fees' => ($reservation->type == self::TYPE_CHECK_UP) ? $clinic->premium_fees : $clinic->premium_follow_up_fees,
                'patient_premium' => ($user->is_premium == 1 && now()->format('Y-m-d') <= $user->expiry_date) ? 1 : 0,
                'doctor_premium' => $doctor->is_premium ?? 0,
                'vat_included' => $clinic->vat_included
            ]);
        }

        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.approved'), 'reservations.index', ['status' => 'approved', 'clinic' => $clinic_id]);
    }


    /**
     *  reschedule reservations
     *
     * @param $id
     * @return Factory|RedirectResponse|View
     */
    public function edit($id)
    {
        $reservation = $this->reservation->getReservationById($id);
        if (!$reservation) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }

        $day_name = Carbon::parse($reservation->day)->format('l');
        if (!$day_name) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.invalid-date'));
        }
        //get day index
        foreach (Config::get('lists.days') as $day) {
            if ($day_name == $day['en_name']) {
                $index = $day['day'];
            }
        }

        $clinic = $reservation->clinic;

        if ($clinic->pattern == self::PATTERN_INTERVAL) {

            $workingHoursRepo = new WorkingHourRepository();
            $reservation->working_hour = $workingHoursRepo->getWorkingHoursById($reservation->working_hour_id);

            if (!$reservation->working_hour) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.working-hours-not-found'));
            }

            $times = $workingHoursRepo->getWorkingHoursInClinicThatIsNotReserved($reservation->clinic_id, $reservation->day, $index);

            if (!$times) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_times_available'));
            }
            if (count($times) == 0) {
                $times = [NULL => trans('lang.no_times_available')];
            } else {
                $times = $times->pluck('time', 'id');
            }
            $reservation->times = $times;
            if (!$reservation->times) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_times_available'));
            }
        }
        return view('admin.common.reservations.edit', compact('reservation', 'clinic'));
    }

    /**
     * @param ReservationRequest $request
     * @param $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(ReservationRequest $request, $id)
    {
        $reservation = $this->reservation->getReservationById($id);
        if (!$reservation) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }

        $auth = auth()->user();

        $request['status'] = self::R_STATUS_APPROVED;
        $request['updated_by'] = $auth->id;
        DB::beginTransaction();

        if ($request->working_hour_id != null) {
            $working_hour = (new WorkingHourRepository())->getWorkingHoursById($request->working_hour_id);
            if (is_object($working_hour)) {
                $expiry_date = $working_hour->expiry_date;
            } else {
                $expiry_date = null;
            }
        } else {
            $expiry_date = null;
        }

        // in case of update reservation check the reservation in that day
        // get the number of attended and approved reservations
        $clinic = ClinicRepository::getClinicById($reservation->clinic_id);
        if (!$clinic) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        $reservation_count = $this->reservation->getCountOfAllReservationsWhichIsApprovedAndAttended($clinic->id, $request->day);
        if ($reservation_count >= $clinic->res_limit) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no-appointments-in-that-day'));
        }

        try {
            // update the Queue number
            if ($clinic->pattern == self::PATTERN_INTERVAL) {

                $working_hours = (new WorkingHourRepository())->getArrayOfWorkingHoursByClinicAndDay($clinic->id, self::getDayIndex($request->day), $expiry_date);

                try {
                    $working_hour_for_reservation = (new WorkingHourRepository())->getWorkingHoursById($request->working_hour_id);
                } catch (Exception $e) {
                    WebController::catchExceptions($e->getMessage());
                    return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation_add_err'));
                }
                $request['queue'] = array_search($working_hour_for_reservation->time, $working_hours) + 1;

            } else {

                // in case of Queue
                $reservation_queue = $this->reservation->getFirstReservationByClinicAndDayOrdered($clinic->id, $request->day);
                // in case there is people in the q
                if ($reservation_queue) {
                    $largest_queue = $reservation_queue->queue;
                    $request['queue'] = $largest_queue + 1;
                } else {
                    // no reservations yet
                    $request['queue'] = 1;
                }
            }

            $reservation = $this->reservation->updateReservation($reservation, $request);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation_update_err'));
        }

        $receiver = (new \App\Http\Repositories\Api\AuthRepository())->getUserById($reservation->user_id);

        if ($receiver) {
            if (!$receiver->lang) {
                $lang = 'en';
            } else {
                $lang = $receiver->lang;
            }

            // create notification to be pushed to user who changed his reservation
            $notification_data = [
                'multicast' => 0, // for user
                'sender_id' => $auth->id,
                'receiver_id' => $reservation->user_id,
                'en_title' => $auth->account['en_name'],
                'ar_title' => $auth->account['ar_name'],
                'en_message' => 'your reservation appointment has been changed',
                'ar_message' => 'لقد تم تغيير ميعاد الحجز الخاص بك',
                'url' => 'reservation',
                'object_id' => $reservation->id,
                'table' => 'reservation',
            ];

            try {
                // create notification to be pushed to user notifying him that reservation status has been changes
                $notification = (new NotificationRepository())->createNewNotification($notification_data);
            } catch (Exception $e) {
                DB::rollBack();
                $this->logErr($e);
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.notifications_failed'), 'reservations.index', ['status' => 'approved']);
            }

            if ($receiver->is_notification == 1) {
                $tokens = (new TokenRepository())->getTokensByUserId($reservation->user_id);
                if ($tokens) {
                    try {
                        $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->url, $notification);
                    } catch (Exception $e) {
                        DB::rollBack();
                        $this->logErr($e);
                        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.notifications_failed'), 'reservations.index', ['status' => 'approved']);
                    }
                }
            }

        }

        DB::commit();

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.reservation_update_ok'), 'reservations.index', ['status' => 'approved', 'clinic' => $clinic->id]);
    }

    /**
     * set reservation status
     *
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function setStatus(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1',
            'status' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return 'false';
        }

        $reservation = $this->reservation->getReservationById($request->id);
        if (!$reservation) {
            return 'false';
        }

        if ($reservation->status == $request['status']) {
            return 'dont do any thing';
        }

        unset($request['id']);
        // update the reservation
        DB::beginTransaction();
        try {
            $reservation = $this->reservation->updateReservation($reservation, $request);
        } catch (Exception $e) {
            DB::rollBack();
            self::logErr($e);
            return 'false';
        }

        switch ($reservation->status) {
            case self::R_STATUS_APPROVED :
                $status = trans('lang.approved');
                break;
            case self::R_STATUS_CANCELED :
                $status = trans('lang.canceled');
                break;
            case self::R_STATUS_ATTENDED :
                $status = trans('lang.attended');
                break;
            case self::R_STATUS_MISSED :
                $status = trans('lang.missed');
                break;
            default:
                $status = trans('lang.get action');
        }

        $auth = auth()->user();

        $receiver = (new \App\Http\Repositories\Api\AuthRepository())->getUserById($reservation->user_id);

        // check for user notification is open or not
        if ($receiver) {
            if (!$receiver->lang) {
                $lang = 'en';
            } else {
                $lang = $receiver->lang;
            }

            $notification_data = [
                'multicast' => 0, //0 => for user, any other number will represent user roles
                'sender_id' => auth()->user()->id,
                'receiver_id' => $reservation->user_id,
                'en_title' => $auth->account['en_name'],
                'ar_title' => $auth->account['ar_name'],
                'en_message' => 'your reservation has been ' . $status,
                'ar_message' => ' الحجز الخاص ' . $status . 'لقد تم',
                'url' => 'reservation',
                'object_id' => $reservation->id,
                'table' => 'reservation',
            ];

            try {
                // create notification to be pushed to user notifying him that reservation status has been changes
                $notification = (new NotificationRepository())->createNewNotification($notification_data);
            } catch (Exception $e) {
                DB::rollBack();
                self::logErr($e);
                return 'false';
            }

            if (!$notification) {
                return 'false';
            }

            if ($receiver->is_notification == 1) {
                $tokens = (new TokenRepository())->getTokensByUserId($notification->receiver_id);
                if ($tokens) {
                    $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->url, $notification);
                }
            }
        }
        DB::commit();

        return 'true';
    }


    /**
     * get reservation status
     *
     * @param $id
     * @return Factory|RedirectResponse|View
     */
    public function getStatus($id)
    {
        $reservation = $this->reservation->getReservationById($id);
        if (!$reservation) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }
        return view('admin.common.reservations.status', compact('reservation'));
    }

    /**
     *  get the reservations according to the status
     *
     * @param $status
     * @param $auth_user
     * @param $today_format
     * @param $clinic
     * @return mixed
     */
    public function getReservationAccordingToStatus($status, $auth_user, $today_format, $clinic)
    {
        $notification_repository = new NotificationRepository();
        //status , account_id , clinic_id
        switch ($status) {
            case 'all' :
                // get all pending reservation
                if (isset($_GET['notification'])) {
                    $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                    if (!$notification_model) {
                        return false;
                    }
                    return $this->reservation->reservation::getReservationsByStatus('all', null, null, $clinic, $notification_model->object_id);
                }
                return $this->reservation->reservation::getReservationsByStatus('all', null, null, $clinic);
                break;

            case 'today' :
                // if get today's reservations for doctor so we get approved reservations only
                if ($auth_user->role_id == self::ROLE_DOCTOR) {
                    if (!isset($_GET['clinic'])) {
                        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
                    }
                    $doctor_clinic = ClinicRepository::getClinicById($_GET['clinic']);
                    if (!$doctor_clinic) {
                        return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
                    }
                    // in case of notifications
                    if (isset($_GET['notification'])) {
                        $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                        if (!$notification_model) {
                            return false;
                        }
                        return $this->reservation->reservation::getReservationsByStatus([self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED, self::R_STATUS_MISSED], Carbon::today('Africa/Cairo')->format('Y-m-d'), null, $doctor_clinic, $notification_model->object_id);
                    }
                    return $this->reservation->reservation::getReservationsByStatus([self::R_STATUS_APPROVED, self::R_STATUS_ATTENDED, self::R_STATUS_MISSED], Carbon::today('Africa/Cairo')->format('Y-m-d'), null, $doctor_clinic, null);

                    // in case of assistant
                }
                if (isset($_GET['notification'])) {
                    $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                    if (!$notification_model) {
                        return false;
                    }
                    return $this->reservation->reservation::getReservationsByStatus([self::R_STATUS_APPROVED, self::R_STATUS_MISSED], Carbon::today('Africa/Cairo')->format('Y-m-d'), null, $clinic, $notification_model->object_id);
                }
                return $this->reservation->reservation::getReservationsByStatus([self::R_STATUS_APPROVED, self::R_STATUS_MISSED], Carbon::today('Africa/Cairo')->format('Y-m-d'), null, $clinic, null);
                break;

            case 'approved' :
                if (isset($_GET['notification'])) {
                    $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                    if (!$notification_model) {
                        return false;
                    }
                    return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_APPROVED, null, null, $clinic, $notification_model->object_id);
                }
                return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_APPROVED, null, null, $clinic);
                break;

            case 'canceled' :
                if (isset($_GET['notification'])) {
                    $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                    if (!$notification_model) {
                        return false;
                    }
                    return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_CANCELED, null, null, $clinic, $notification_model->object_id);
                }
                return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_CANCELED, null, null, $clinic);
                break;

            case 'attended' :
                if (isset($_GET['notification'])) {
                    $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                    if (!$notification_model) {
                        return false;
                    }
                    return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_ATTENDED, null, null, $clinic, $notification_model->object_id);
                }
                return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_ATTENDED, null, null, $clinic, null);
                break;

            case 'missed' :
                if (isset($_GET['notification'])) {
                    $notification_model = $notification_repository->getNotificationById($_GET['notification']);
                    if (!$notification_model) {
                        return false;
                    }
                    return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_MISSED, null, null, $clinic, $notification_model->object_id);
                }
                return $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_MISSED, null, null, $clinic);
                break;

            default :
                return false;
        }
    }

// *************************** Ajax Area ******************************************************************************************
    // get reserved times to set in drop down menu
    public function timeReserved(Request $request)
    {
        if (!$request->day) {
            return false;
        }

        $clinic_id = (auth()->user()->role_id == self::ROLE_ASSISTANT) ? auth()->user()->clinic_id : ($request->clinic_id ?? null);
        //get the current clinic
        $clinic = ClinicRepository::getClinicById($clinic_id);
        if (!$clinic) {
            return false;
        }

        $date = Carbon::parse($request->day)->format('l');

        foreach (Config::get('lists.days') as $day) {
            if ($date == $day['en_name']) {
                $index = $day['day'];
            }
        }

        // in case of intervals
        if ($clinic->pattern == self::PATTERN_INTERVAL) {
            // get available working hours in the given day
            $working_hours = (new WorkingHourRepository())->getWorkingHoursInClinicThatIsNotReserved($clinic->id, $request->day, $index);
            if (!$working_hours) {
                return false;
            }
            return $working_hours;

        } else {
            // get the number of attended and approved reservations
            $reservation_count = $this->reservation->getCountOfAllReservationsWhichIsApprovedAndAttended($clinic->id, $request->day);
            if ($reservation_count >= $clinic->res_limit) {
                return 'false';
            }

            // check if the day of reservation is in the range of working hours or not
            $working_hours_range = (new WorkingHourRepository())->checkIfDayOfReservationInRange($clinic->id, $request->day, $index);

            if ($working_hours_range) {
                if (count($working_hours_range) == 0) {
                    return 'false';
                }
            }

            // get breaks in case there was breaks
            $breaks = (new WorkingHourRepository())->getBreakWorkingHoursByClinicId($clinic->id, $index);
            if ($breaks && count($breaks) > 0) {
                $break_times = '<b><p class="bold">Breaks:</p></b>';
                foreach ($breaks as $break) {
                    $break_times .= '<p><b>FROM</b>  ' . Carbon::parse($break->min_time)->format('h:i A') . ' <b>TO</b> ' . Carbon::parse($break->max_time)->format('h:i A') . '</p>';
                }
                return $break_times;
            }

            return 'true';
        }
    }

    // filter reservations by date or name
    public function getFilteredReservation($status = NULL, $day = NULL, $name = NULL)
    {
        $auth_user = auth()->user();

        $clinic_id = (auth()->user()->role_id == self::ROLE_ASSISTANT) ? auth()->user()->clinic_id : ($_GET['clinic'] ?? null);

        $clinic = Clinic::where('id', $clinic_id)->first();
        if (!$clinic) {
            return '';
        }

        switch ($status) {
            case 'all' :
                $reservations = $this->reservation->reservation::getReservationsByStatus('all', $day, $name, $clinic);
                break;
            case 'today' :
                // if get today's reservations for doctor so we get approved reservations only
                $reservations = $this->reservation->reservation::getReservationsByStatus([self::R_STATUS_APPROVED, self::R_STATUS_MISSED], Carbon::today("Africa/Cairo")->format('Y-m-d'), $name, $clinic);
                break;
            case 'approved' :
                $reservations = $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_APPROVED, $day, $name, $clinic);
                break;
            case 'canceled' :
                $reservations = $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_CANCELED, $day, $name, $clinic);
                break;
            case 'attended' :
                $reservations = $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_ATTENDED, $day, $name, $clinic);
                break;
            case 'missed' :
                $reservations = $this->reservation->reservation::getReservationsByStatus(self::R_STATUS_MISSED, $day, $name, $clinic);
                break;
            default :
                return 'false';
        }

        $queue = ClinicQueue::where('clinic_id', $auth_user->clinic_id)->whereDate('updated_at', '=', Carbon::today("Africa/Cairo")->toDateString())->first();
        return view('admin.common.reservations.table-reservations', compact('reservations', 'queue'));
    }

    // refresh all results on key up
    public function refreshResults()
    {
        // get all the patients mobiles
        $data = (new AuthRepository())->getAllPatientsMobiles();
        if (!$data) {
            $data = [];
        }
        return implode("#", $data);
    }

    // return the name of a specific mobile
    public function userResults(Request $request)
    {
        if (!$request['mobile'] || empty($request['mobile'])) {
            return false;
        }
        // get the name using mobile
        $users_name = (new AuthRepository())->getPatientUsingMobile($request['mobile']);
        if (!$users_name) {
            return false;
        }
        return $users_name->name;
    }

    /**
     *  check given data from assistant
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDate(Request $request)
    {

        if (!isset($request->day) || !isset($request->pattern)) {
            return response()->json(['status' => false, 'msg' => trans('lang.choose_day_not_found')]);
        }
//        $clinic_id = auth()->user()->clinic_id;
        $clinic_id = (auth()->user()->role_id == self::ROLE_ASSISTANT) ? auth()->user()->clinic_id : ($request->clinic ?? null);

        // check day if holiday or not
        $holiday_exists = (new HolidayRepository())->getHolidayByDayAndClinic($request->day, $clinic_id);
        if ($holiday_exists) {
            return response()->json(['status' => false, 'msg' => trans('lang.we-do-not-work-on-holidays')]);
        }
        // pass in case of interval
        if ($request->pattern == self::PATTERN_INTERVAL) {
            return response()->json(['status' => true]);
        }

        $dayIndex = self::getDayIndex($request->day);

        // check if there is working hours in that day
        $times_available = (new WorkingHourRepository())->getArrayOfWorkingHoursByClinicAndDay($clinic_id, $dayIndex);

        if (is_array($times_available) && count($times_available) < 2) {
            return response()->json(['status' => false, 'msg' => trans('lang.no-appointments-in-that-day')]);
        }
        // check if number of users exceeded the limit of reservations or not
        try {
            // get the count of confirmed or attended users that has reservations in that day
            $number_of_reserved_persons = $this->reservation->getCountOfAllReservationsWhichIsApprovedAndAttended($clinic_id, $request->day);
            if ($number_of_reserved_persons == -1) {
                return response()->json(['status' => false, 'msg' => trans('lang.no-appointments-in-that-day')]);
            }
            $limit_number_of_users = ClinicRepository::getClinicById($clinic_id)->res_limit;
            if (!$limit_number_of_users) {
                return response()->json(['status' => false, 'msg' => trans('lang.no-appointments-in-that-day')]);
            }

            if ($number_of_reserved_persons >= $limit_number_of_users) {
                return response()->json(['status' => false, 'msg' => trans('lang.no-appointments-in-that-day')]);
            }

        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => trans('lang.no-appointments-in-that-day')]);
        }

        return response()->json(['status' => true]);
    }

    /**
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function setCashReservationPaid(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            return 'false';
        }
        // get reservation by id
        $reservation = $this->reservation->getReservationById($request->id);
        if (!$reservation) {
            return 'false';
        }

        // check if reservation is cash and not paid
        if ($reservation->payment_method != 0 && $reservation->transaction_id != -1) {
            return 'false';
        }

        DB::beginTransaction();
        try {
            $reservation = $this->reservation->setReservationToPaid($reservation, auth()->user()->id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return 'false';
        }

        if ($reservation->transaction_id == -2) {
            return 'true';
        }
        return 'false';
    }

    /**
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function checkTransaction(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1',
            'transaction' => 'required|numeric|min:1'
        ]);
        if ($validator->fails()) {
            return 'false';
        }
        // get reservation by id
        $reservation = $this->reservation->getReservationById($request->id);
        if (!$reservation) {
            return 'false';
        }

        // check if reservation is cash and not paid
        if ($reservation->payment_method == 0 || $reservation->transaction_id == -1) {
            return 'false';
        }

        if ($reservation->transaction_id != $request['transaction']) {
            return 'false';
        }

        DB::beginTransaction();
        try {
            // change status to be attended
            $reservation = $this->reservation->changeReservationStatus($reservation, self::R_STATUS_ATTENDED);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return 'false';
        }

        return $reservation->status === self::R_STATUS_ATTENDED ? 'true' : 'false';
    }

    /**
     *  set user as stand By
     *
     * @param $reservation_id
     * @return RedirectResponse
     * @throws Exception
     */
    public function setStandBy($reservation_id)
    {
        // get the reservation
        $reservation = $this->reservation->getReservationById($reservation_id);

        if (!$reservation) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }

        // check if reservation is missed or not
        if ($reservation->status != self::R_STATUS_MISSED) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }

        // check if there is already standBy reservations or not
        $standBy = new StandByRepository();

        if ($standBy->getStandBy($reservation->clinic_id)) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.already-there-is-standBy'));
        }

        // put the reservation in stand by
        DB::beginTransaction();


        // check if the reservation in queue missed or attended
        // then directly put the reservation as next in Queue
        // get clinic queue
        $clinicQueue = new ClinicQueueRepository();       // new instance from clinic Queue
        $queue = $clinicQueue->getClinicQueueByClinic($reservation->clinic_id);
        if (!$queue) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.queue_not_started'));
        }
        $previous_reservation = $this->reservation->getReservationByStatusAndClinic('', $reservation->clinic_id, $queue->queue);
        if (!$previous_reservation) {
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_reservations'));
        }
        // in case the current reservation is missed or attended
        if (in_array($previous_reservation->status, [self::R_STATUS_ATTENDED, self::R_STATUS_MISSED])) {
            // directly put the standby reservation in queue
            // first change the status
            $this->reservation->changeReservationStatus($reservation, self::R_STATUS_APPROVED);
            // the put it in the queue
            $reservation = $this->reservation->addReservationCheckInAndOut($reservation, auth()->user()->id);
            $clinicQueue->setQueueToNextReservation($queue, $reservation->queue);

        } else {

            try {
                $standBy->setStandBy($reservation_id, $reservation->queue, $reservation->clinic_id);
                $this->reservation->changeReservationStatus($reservation, self::R_STATUS_APPROVED);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.standBy'));
            }
        }
        // change the status to attended
        // return success message
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.standBy_ok'));
    }


    /**
     *  account reservations
     *
     * @param null $id
     * @return Factory|RedirectResponse|View
     */
    public function getAccountReservations($id = null)
    {
        if ($id) {
            // set missed reservation in previous day
            $reservations = $this->reservation->getAccountReservations($id);
            if ($reservations == false) {
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.something_wrong'));
            }
            return view('admin.rk-admin.reservations-statistics.dashboard_reservations', compact('reservations'));
        }
        return redirect()->back();
    }

    public function getAllAccountsReservations()
    {
        $reservations_statistics = $this->reservation->getLastReservationsCountWithDoctors();
        if ($reservations_statistics == false) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.something_wrong'));
        }
        return view('admin.rk-admin.reservations-statistics.index', compact('reservations_statistics'));
    }


    /**
     *
     * @param $id
     * @return Factory|RedirectResponse|View
     */
    public function getReservationDetails($id)
    {
        $reservation = $this->reservation->getReservationById($id);

        if (!$reservation) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.reservation-not-found'));
        }
        $payment = (new \App\Http\Repositories\Api\ReservationRepository())->getReservationFeesAfterReservation($reservation->id);
        $offer = $reservation->offer_id === null ? null : (new OfferRepository())->getOfferById($reservation->offer_id);
        return view('admin.common.reservations.details', compact('reservation', 'payment', 'offer'));
    }
}
