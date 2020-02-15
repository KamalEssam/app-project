<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\ClinicRepository;
use App\Http\Repositories\Web\ReservationRepository;
use App\Http\Repositories\Web\WorkingHourRepository;
use App\Http\Requests\WorkingHourRequest;
use App\Http\Traits\DateTrait;
use App\Http\Traits\UserTrait;
use App\Models\Holiday;
use App\Models\Reservation;
use Carbon\Carbon;
use DB;
use Config;
use Illuminate\Http\Request;

class WorkingHourController extends WebController
{
    use DateTrait, UserTrait;

    /**
     *  get list of working hours and in the current assistant clinic
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (auth()->user()->role_id == self::ROLE_DOCTOR && isset($_GET['clinic'])) {
            $clinic_id = $_GET['clinic'];
        } else {
            $clinic_id = auth()->user()->clinic_id;
        }
        // get the clinic for the current assistant
        $clinic = ClinicRepository::getClinicById($clinic_id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        $day_indexes = [];
        // get the list of the days stored in the config file
        foreach (Config::get('lists.days') as $item) {
            $day = new \stdClass();
            $day->day = $item['day'];
            $day_indexes[] = $day;
            $day_indexes[$day->day]->working_hours = (new WorkingHourRepository())->getWorkingHoursByDayAndClinicId($day->day, $clinic->id);
        }
        $holidays = Holiday::where('clinic_id', $clinic->id)->select('en_reason', 'ar_reason', 'day_index', 'day')->get();

        return view('admin.assistant.working-hours.index', compact('day_indexes', 'clinic', 'holidays'));

    }

    /**
     * Show the form for creating a new appointments.
     *
     * @param null $clinic_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create($clinic_id = null)
    {
        $auth_user = auth()->user();
        // get the clinic
        if ($auth_user->role_id == self::ROLE_DOCTOR && $clinic_id != null) {
            $clinic = ClinicRepository::getClinicById($clinic_id);
        } else {
            $clinic = ClinicRepository::getClinicById($auth_user->clinic_id);
        }

        // get clinic that we need it's working hours
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        // get clinic account
        $clinic_account = self::getAccountById($clinic->account_id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
        }

        $days = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, true);
        if ($days == false) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.working_hours_went_wrong'));
        }

        $upcoming_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, false);

        $day_indexes = [];

        foreach ($days as $i => $day) {
            $day_indexes[$i] = $day->day;
        }

        return view('admin.assistant.working-hours.create', compact('clinic', 'day_indexes', 'days', 'clinic_account', 'upcoming_workingHours'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkingHourRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(WorkingHourRequest $request)
    {
        // update hour 00:00 to 23:59 in stead
        if ($request->to == '00:00') {
            $request->to = '23:59';
        }
        $auth_user = auth()->user();

        // get clinic by using clinic id in user table
        $clinic = ClinicRepository::getClinicById($request->clinic_id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }

        DB::beginTransaction();
        try {
            $from = Carbon::createFromFormat('H:i', $request->from);
            $to = Carbon::createFromFormat('H:i', $request->to);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.invalid-date'));
        }

        $workingHoursRepository = new WorkingHourRepository();
        // if clinic work intervals
        if ($clinic->pattern == self::PATTERN_INTERVAL) {
            // get the reservation average
            $interval = $clinic->avg_reservation_time;
            if (empty($interval)) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.specify_interval_avg'));
            }

            try {
                if (empty($request->start_date)) {
                    $start_date = now()->format('Y-m-d');
                } else {
                    $start_date = $request->start_date;
                    (new WorkingHourRepository())->UpdateOldWorkingHoursWithExpiry_date($request->clinic_id, $request->day, $start_date);
                }
                $workingHoursRepository->createNewWorkingHours($request->clinic_id, $request->day, $from, $start_date);

            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.workingHour_add_err'));
            }

            // loop the from and to Dates => then add appointments to the Database
            while ($from < $to) {
                try {
                    $from = Carbon::parse($from)->addMinutes($interval);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.invalid-date'));
                }

                try {
                    $workingHoursRepository->createNewWorkingHours($request->clinic_id, $request->day, $from, $start_date);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.invalid-date'));
                }
            }
        } // if clinic work queue
        else {
            try {
                if (empty($request->start_date)) {
                    $start_date = now()->format('Y-m-d');
                } else {
                    $start_date = $request->start_date;
                    (new WorkingHourRepository())->UpdateOldWorkingHoursWithExpiry_date($request->clinic_id, $request->day, $start_date);
                }

                // add from and to Appointments to Database
                $workingHoursRepository->createNewWorkingHours($request->clinic_id, $request->day, $from, $start_date);
                $workingHoursRepository->createNewWorkingHours($request->clinic_id, $request->day, $to, $start_date);
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.invalid-date'));
            }
        }

        // cancel reservation when add future working hours
        if ($request->start_date != null) {
            // include day index in case of queue clinic
            $daysName = [];
            foreach ($request->day as $day) {
                $daysName[] = Config::get('lists.days')[$day]['en_name'];
            }
            (new NotificationController())->cancelReservationsFromStartingDate($request->clinic_id, $daysName, $request->start_date);
        }

        DB::commit();

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.working_hour_added_ok'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit()
    {
        // if the appointment day is not existing or empty then report an error
        if (!isset($_GET['day']) || !in_array($_GET['day'], [0, 1, 2, 3, 4, 5, 6]) || !isset($_GET['clinic'])) {
            abort('404');
        }
        $clinic_id = $_GET['clinic'];
        $day = $_GET['day'];


        $auth_user = auth()->user();
        // get the clinic
        if ($auth_user->role_id == self::ROLE_DOCTOR && !is_null($clinic_id)) {
            $clinic = ClinicRepository::getClinicById($clinic_id);
        } else {
            $clinic = ClinicRepository::getClinicById($auth_user->clinic_id);
        }

        // get clinic that we need it's working hours
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.clinic_not_found'));
        }
        // get clinic account
        $clinic_account = self::getAccountById($clinic->account_id);
        if (!$clinic) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.account_not_found'));
        }
        // get days and working hours belong to this clinic
        $days = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id);
        if ($days == false) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.working_hours_went_wrong'));
        }
        $day_indexes = [];

        foreach ($days as $i => $day) {
            $day_indexes[$i] = $day->day;
        }

        return view('admin.assistant.working-hours.edit', compact('clinic', 'day_indexes', 'days', 'clinic_account'));

    }


    /***************************************AJAX*****************************************/
    /**
     *  check if the day assistant want to change dates in - is today or not
     *
     * @param null $clinic_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkValue($clinic_id = null)
    {
        $check_value = new \stdClass();
        $auth_user = auth()->user();

        $day = $_GET['day'];
        // if the appointment day is not existing or empty then report an error
        if ($_GET['day'] == null || !in_array($day, [0, 1, 2, 3, 4, 5, 6])) {
            $check_value->check_visit = "true";
            return response()->json($check_value);
        }

        $day_name = Config::get('lists.days')[$day][app()->getLocale() . '_name'];

        if ($clinic_id != null && $auth_user->role_id == self::ROLE_DOCTOR) {
            $the_clinic = ClinicRepository::getClinicById($clinic_id);
            if (!$the_clinic) {
                $check_value->check_visit = "false";
                return response()->json($check_value);
            }
            $working_hours = (new WorkingHourRepository())->getIdsOfWorkingHoursByClinicIdAndDay($the_clinic->id, $day);
        } else {
            // get working hours start and end if exist
            $working_hours = (new WorkingHourRepository())->getIdsOfWorkingHoursByClinicIdAndDay($auth_user->clinic_id, $day);
        }
        $today = Carbon::now('Africa/Cairo');
        // TODO move this to Reservation Repository
        // reservations that will be cancel if we changed working hours
        $reservations = Reservation::whereIn('working_hour_id', $working_hours)
            ->where('day', '>=', $today->format('Y-m-d'))
            ->whereIn('status', [0, 1])
            ->get();

        $check_value->reservations_count = $reservations->count();

        $today_day_name = $today->format('l');

        if (count($working_hours) > 0 && $check_value->reservations_count > 0) {
            $check_value->check_visit = 'true';
        } else {
            $check_value->check_visit = 'false';
        }

        // check if checked day is Today or not
        if ($today_day_name == $day_name) {
            $check_value->check_today = 'true';
        }

        return response()->json($check_value);
    }

    /**
     *  soft delete the appointments in the given day index
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $day = $request['day'];
        $clinic_id = $request['clinic_id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        // if the appointment day is not existing or empty then report an error
        if ($day == null || !in_array($day, [0, 1, 2, 3, 4, 5, 6])) {
            return response()->json(['msg' => false], 200);
        }

        if ($clinic_id != null && auth()->user()->role_id == self::ROLE_DOCTOR) {
            $clinic = $clinic_id;
        } else {
            $clinic = auth()->user()->clinic_id;
        }

        $the_clinic = ClinicRepository::getClinicById($clinic);
        if (!$the_clinic) {
            return response()->json(['msg' => false], 200);
        }

        // loop the working hours in the given day and delete then
        $working_hours_in_day = (new WorkingHourRepository())->getWorkingHoursByDayAndClinicIdAndStartingDay($day, $clinic, $start_date);
        if (count($working_hours_in_day) < 0) {
            return response()->json(['msg' => false], 200);
        }
        foreach ($working_hours_in_day as $appointment) {
            $appointment->delete();
        }

        // include day index in case of queue clinic
        $day_index = ($the_clinic->pattern == self::PATTERN_QUEUE) ? $day : null;

        (new NotificationController())->canceledWhenChangeWorkingHours($clinic, $the_clinic->pattern, $day_index, $start_date, $end_date);

        if (is_null($end_date)) {
            (new WorkingHourRepository())->UpdateOldWorkingHoursWhenDeleteNew($clinic_id, $day, $start_date);
        }

        return response()->json(['msg' => true], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function WorkingHoursCheck(Request $request)
    {
        // case 1 check if time is valid
        if ($request->from != null && $request->to != null) {
            if (Carbon::parse($request->from, 'Africa/Cairo') >= Carbon::parse($request->to, 'Africa/Cairo')) {
                return response()->json(['status' => false, 'case' => 1, 'msg' => 'unrecognized time frame'], 200);
            }
        } else {
            return response()->json(['status' => false, 'case' => 1, 'msg' => 'unrecognized time frame'], 200);
        }

        // case 2
        if (isset($request->clinic_id, $request->dayIndex) && is_array($request->dayIndex)) {
            // get all the reservations that is in that set of days and in the given time period
            $day_exists = (new WorkingHourRepository())->checkIfWorkingHoursExistsOrNot($request->clinic_id, $request->dayIndex, $request->start_date);
            if ($day_exists) {
                return response()->json(['status' => false, 'case' => 2, 'msg' => 'there is already working hours in that days please delete it first'], 200);
            }
        } else {
            return response()->json(['status' => true, 'case' => -1, 'msg' => $request->clinic_id], 200);
        }

        // case 3  check if there is reservations in that day or not
        if (isset($request->clinic_id, $request->dayName) && is_array($request->dayName)) {
            // get all the reservations that is
            // 1- in that clinic that equal to   $request->clinic_id
            // 2- the dayIndex of it equal to    $request->dayIndex
            // 3- that is after today
            $reservation_count = (new ReservationRepository())->getReservationsCountInClinicAndDay($request->clinic_id, $request->dayName, $request->start_date, $request->end_date);
            // first case reservations after certain date
            return response()->json(['status' => true, 'case' => 3, 'reservations' => $reservation_count], 200);
        }

        return response()->json(['status' => true, 'case' => -1], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNumberOfReservationsOnWorkingHours(Request $request)
    {
        if (isset($request->clinic_id, $request->dayName) && ($request->start_date)) {
            // get all the reservations that is
            // 1- in that clinic that equal to   $request->clinic_id
            // 2- the dayIndex of it equal to    $request->dayIndex
            // 3- that is after today
            $reservation_count = (new ReservationRepository())->getReservationsCountInClinicAndDay($request->clinic_id, [$request->dayName, 'Friday'], $request->start_date, $request->end_date);
            // first case reservations after certain date
            return response()->json(['status' => true, 'reservations' => $reservation_count], 200);
        }
        return response()->json(['status' => false], 200);
    }

    /**
     *  add breaks times
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBreak(Request $request)
    {
        if (!$request->to || !$request->from || count($request->deyIndexes) == 0 || !$request->clinic_id) {
            return response()->json(['msg' => false, 'err' => 'Please fill all information to add Break'], 200);
        }

        $from = $request->from . ':00';
        $to = $request->to . ':00';
        $daysIndexes = $request->deyIndexes;
        $clinic_id = $request->clinic_id;

        $clinic = (new \App\Http\Repositories\Api\ClinicRepository())->getClinicById($clinic_id);

        if ($clinic->pattern == self::PATTERN_INTERVAL) {
            $breaks_added = (new WorkingHourRepository())->updateIntervalWorkingHoursToAddBreaks($clinic_id, $from, $to, $daysIndexes);
            if ($breaks_added === false) {
                return response()->json(['msg' => false, 'err' => 'there is days selected dont have working hours'], 200);
            }
        } else {
            // add records with breaks
            $breaks_added = (new WorkingHourRepository())->addWorkingHourForQueueBreaks($clinic_id, $from, $to, $daysIndexes);
            if ($breaks_added === false) {
                return response()->json(['msg' => false, 'err' => 'there is days selected dont have working hours'], 200);
            }
        }

        return response()->json(['msg' => true], 200);
    }

    public function deleteBreaks(Request $request)
    {
        if ($request->day == null || !$request->updated_at || $request->clinic_id == null) {
            return response()->json(['msg' => false], 200);
        }

        try {
            $clinic = (new \App\Http\Repositories\Api\ClinicRepository())->getClinicById($request->clinic_id);

            if ($clinic) {

                if ($clinic->pattern == self::PATTERN_INTERVAL) {
                    // delete working hours using day and date and clinic id
                    (new WorkingHourRepository())->deleteIntervalBreaksFromClinic($request->clinic_id, $request->day, $request->updated_at);
                    return response()->json(['msg' => true], 200);
                } else {
                    // in case of Queue
                    (new WorkingHourRepository())->deleteQueueBreaksFromClinic($request->clinic_id, $request->day, $request->updated_at);
                    return response()->json(['msg' => true], 200);
                }

            } else {
                return response()->json(['msg' => false], 200);
            }
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return response()->json(['msg' => false], 200);
        }
    }
}
