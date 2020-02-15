<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\ReservationInterface;
use App\Http\Repositories\Web\OfferRepository;
use App\Http\Repositories\Web\ParentRepository;
use App\Http\Repositories\Web\UserRepository;
use App\Http\Traits\UserTrait;
use App\Models\Clinic;
use App\Models\ClinicQueue;
use App\Models\Holiday;
use App\Models\Reservation;
use App\Models\ReservationsPayment;
use App\Models\User;
use App\Models\WorkingHour;
use App\Http\Traits\DateTrait;
use Carbon\Carbon;
use DB;

class ReservationRepository extends ParentRepository implements ReservationInterface
{
    use DateTrait, UserTrait;
    private $getFees;

    /**
     * get status name (0=>pending, 1=>approved, 2=>canceled, 3=>attended, 4=>missed)
     * @param $status
     * @return mixed
     */
    public function getStatusName($status)
    {
        switch ($status) {
            case 1 :
                $status = trans('lang.approved');
                break;
            case 2 :
                $status = trans('lang.canceled');
                break;
            case 3 :
                $status = trans('lang.attended');
                break;
            case 4 :
                $status = trans('lang.missed');
                break;
            default:
                $status = trans('lang.get_action');
        }
        return $status;
    }

    /**
     * get clinic by id
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicById($clinic_id)
    {
        return Clinic::find($clinic_id);
    }

    /**
     * get reservation by id
     * @param $reservation_id
     * @return mixed
     */
    public function getReservationById($reservation_id)
    {
        return Reservation::find($reservation_id);
    }


    /**
     * get reservation by id
     * @param $reservation_id
     * @return mixed
     */
    public function getReservationWithReview($reservation_id)
    {
        return Reservation::where('id', $reservation_id)->with('clinic')->withCount('review as reviews')->first();
    }

    /**
     * get working hour by id
     * @param $working_hour_id
     * @return mixed
     */
    public function getWorkingHourById($working_hour_id)
    {
        return WorkingHour::find($working_hour_id);
    }

    /**
     * check if working hour belong to this day and this clinic
     * @param $working_hour_id
     * @param $clinic
     * @param $day
     * @return mixed
     */
    public function checkWorkingHourBelongToClinic($working_hour_id, $clinic, $day)
    {
        return WorkingHour::where('id', $working_hour_id)
            ->where('clinic_id', $clinic->id)
            ->where('is_break', 0)
            ->where('day', self::getDayIndex($day))
            ->first();
    }

    /**
     * check if working hour belong to this day and this clinic
     * @param $working_hour_id
     * @param $day
     * @return mixed
     */
    public function checkIfWorkingHourReserved($working_hour_id, $day)
    {
        return Reservation::where('working_hour_id', $working_hour_id)
            ->where('status', '<>', ApiController::STATUS_CANCELED)
            ->where('day', $day)
            ->first();
    }

    /**
     * get all clinics with same created by with this clinic
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicsWithSameCreatedBy($clinic_id)
    {
        $clinic = $this->getClinicById($clinic_id);
        if (!$clinic) {
            return false;
        }
        try {
            $clinics = Clinic::where('created_by', $clinic->created_by)->pluck('id')->toArray();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $clinics;
    }

    public function getClinicsWithSameAccount($clinic_id)
    {
        $clinic = $this->getClinicById($clinic_id);
        if (!$clinic) {
            return false;
        }
        try {
            $clinics = Clinic::where('account_id', $clinic->account_id)->pluck('id')->toArray();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $clinics;
    }

    /**
     * check if clinic start queue today
     * @param $clinic_id
     * @return mixed
     */
    public function getQueueToday($clinic_id)
    {
        return ClinicQueue::where('clinic_id', $clinic_id)
            ->whereDate('updated_at', '=', self::getToday())
            ->first();
    }

    /**
     * check if patient have completed visit today
     * @param $clinic_id
     * @param $user_id
     * @param $day
     * @return mixed
     */
    public function CheckPatientCompletedVisitToday($clinic_id, $user_id, $day)
    {
        return Reservation::Join('visits', 'visits.reservation_id', 'reservations.id')
            ->where('visits.clinic_id', $clinic_id)
            ->where('reservations.clinic_id', $clinic_id)
            ->where('reservations.user_id', $user_id)
            ->whereDate('visits.created_at', '=', $day)
            ->first();
    }

    /**
     * check if patient have reservation past
     * @param $user_id
     * @return mixed
     */
    public function checkIfReservationPast($user_id, $offset, $limit)
    {
        // if user have reservation upcoming
        try {
            $user_reservations = Reservation::join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('accounts', 'accounts.id', 'clinics.account_id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                ->where('reservations.user_id', $user_id)
                ->where('reservations.status', '!=', ApiController::STATUS_APPROVED)
                ->where(function ($query) {
                    $query->where('reservations.day', '<=', self::getDateByFormat(self::getToday(), 'Y-m-d'));
                    $query->orWhere('reservations.day', '=', self::getDateByFormat(self::getToday(), 'Y-m-d'));
                    $query->whereRaw('working_hours.time <= IF(working_hours.time IS NOT NULL,?, NULL)', [self::parseDate(self::getTimeByFormat(Carbon::now(), 'g:i a'))]);
                })
                ->select(
                    'working_hours.time',
                    'reservations.id',
                    'reservations.status',
                    'reservations.user_id',
                    'reservations.day',
                    'reservations.offer_id',
                    'reservations.clinic_id',
                    'reservations.type',
                    'reservations.clinic_id',
                    'reservations.cashback_status',
                    'reservations.payment_method', // reservation payment method
                    'clinics.lng',
                    'clinics.lat', 'clinics.pattern',
                    'reservations.queue',
                    'clinics.avg_reservation_time',
                    'accounts.' . app()->getLocale() . '_name as clinic_name'
                )
                ->orderBy('reservations.day', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();
//                ->reject(function ($value, $key) {
//                    // remove the days with no working hours
//                    $user_reservation = json_decode($value);
//                    if ($user_reservation->day == self::getDateByFormat(self::getToday(), 'Y-m-d')) {
//                        if (isset($user_reservation->time) && self::parseDate(self::getTimeByFormat($user_reservation->time, 'g:i a')) < self::parseDate(self::getTimeByFormat(Carbon::now(), 'g:i a'))) {
//                            return true;
//                        }
//                    }
//                    return false;
//                })
//                ->values();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $user_reservations;
    }


    /**
     * check if patient have reservation upcoming , $check == 0 if we call it in add reservation , 1 if call it in get upcoming reservation
     * @param $user_id
     * @param $check
     * @param null $clinic_id
     * @return bool
     */
    public function checkIfReservationUpcoming($user_id, $check = null, $clinic_id = null)
    {
        // get all clinics with same created by to know if patient can reserve in this clinic
        $clinics = $this->getClinicsWithSameAccount($clinic_id);

        // if user have reservation upcoming
        try {
            $user_reservations = Reservation::leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('accounts', 'accounts.id', 'clinics.account_id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->where(function ($query) use ($check, $clinics) {
                    if ($check != null && $check == 0) {
                        $query->whereIn('reservations.clinic_id', $clinics);
                    }
                })
                ->where('reservations.status', ApiController::STATUS_APPROVED)
                ->where('reservations.user_id', $user_id)
                ->where('reservations.day', '>=', self::getDateByFormat(self::getToday(), 'Y-m-d'))
                ->select(
                    'working_hours.time',
                    'reservations.id',
                    'reservations.status',
                    'reservations.user_id',
                    'reservations.day',
                    'reservations.payment_method',
                    'reservations.transaction_id',
                    'reservations.offer_id',
                    'reservations.clinic_id',
                    'reservations.type',
                    'reservations.clinic_id',
                    'clinics.lng',
                    'clinics.lat',
                    'clinics.pattern',
                    'reservations.queue',
                    'clinics.avg_reservation_time',
                    'accounts.' . app()->getLocale() . '_name as clinic_name'
                )
                ->orderBy('reservations.day', 'asc')
                ->get();

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        if ($check != null && $check == 0) {
            if (count($user_reservations) > 0) {
                foreach ($user_reservations as $user_reservation) {
                    if ((isset($user_reservation->time) && $user_reservation->day == self::getDateByFormat(self::getToday(), 'Y-m-d')
                            && self::parseDate(self::getTimeByFormat($user_reservation->time, 'g:i a')) > self::parseDate(self::getTimeByFormat(Carbon::now(), 'g:i a')))
                        || $user_reservation->day != self::getDateByFormat(self::getToday(), 'Y-m-d')
                        || ($user_reservation->day == self::getDateByFormat(self::getToday(), 'Y-m-d') && $user_reservation->time == null
                            && count($user_reservations) == 2)) {
                        return false;
                    }
                }
            } else {
                return true;
            }
        } else {
            return $user_reservations;
        }
    }

    /**
     * check if patient want to reserve in time passed
     * @param $day
     * @param $time
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function checkTimePassed($day, $time)
    {
        //if patient want to reserve in time passed
        if (self::parseDate($day) == self::getDateByFormat(Carbon::today('Africa/Cairo'), 'Y-m-d')
            && self::parseDate($time) < self::parseDate(self::getTimeByFormat(Carbon::now(), 'g:i a'))) {
            return false;
        }
        return true;
    }

    /**
     * update reservation status and created by
     * @param $reservation
     * @param $user_id
     * @param null $largest_queue
     * @return mixed
     */
    public function updateReservationData($reservation, $user_id, $largest_queue = null)
    {
        try {
            $clinic = $this->getClinicById($reservation->clinic_id);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        if ($clinic->pattern == ApiController::PATTERN_INTERVAL) {
            try {
                $working_hour_for_reservation = $this->getWorkingHourById($reservation->working_hour_id);
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
            if (is_object($working_hour_for_reservation)) {
                $expiry_date = $working_hour_for_reservation->expiry_date;
            } else {
                $expiry_date = null;
            }

            $working_hours = (new \App\Http\Repositories\Web\WorkingHourRepository())->getArrayOfWorkingHoursByClinicAndDay($clinic->id, self::getDayIndex($reservation->day), $expiry_date);

            $key_for_working_hour = array_search($working_hour_for_reservation->time, $working_hours) + 1;
        }

        try {
            $reservation->status = 1;
            $reservation->created_by = $user_id;
            $reservation->user_id = $user_id;
            $reservation->queue = ($clinic->pattern == ApiController::PATTERN_QUEUE) ? $largest_queue + 1 : $key_for_working_hour;
            $reservation->update();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $reservation;
    }

    /**
     * get largest queue to increase it in new reservation
     * @param $clinic_id
     * @param $day
     * @return mixed
     */
    public function getLargestQueue($clinic_id, $day)
    {
        return Reservation::where('clinic_id', $clinic_id)
            ->where('day', $day)
            ->whereIn('status', [ApiController::STATUS_APPROVED, ApiController::STATUS_ATTENDED])
            ->orderBy('queue', 'desc')
            ->first();
    }

    /**
     * create new reservation
     * @param $request
     * @return mixed
     */
    public function createReservation($request)
    {
        try {
            DB::beginTransaction();
            $reservation = Reservation::create($request->all());
            if (!$reservation) {
                DB::rollBack();
                return false;
            }
            DB::commit();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $reservation;
    }

    /*****************************************getReservation*********************/
    /************************************** functions for get patient count
     * /**
     * get min working hours to get start clinic
     * @param $clinic_id
     * @param $index
     * @param $day
     * @return bool
     */
    public function getClinicStartTime($clinic_id, $index, $day)
    {

        // get the day of reservation start date and expiry date region
        $min_max_of_workingHours = (new \App\Http\Repositories\Web\WorkingHourRepository())->getMinAndMaxOfWorkingHours($index, $clinic_id);
        $start = null;
        $end = null;
        if (!is_null($min_max_of_workingHours)) {
            if (Carbon::parse($min_max_of_workingHours->min_date) == Carbon::parse($min_max_of_workingHours->max_date)) {
                $start = $min_max_of_workingHours->min_date;
            } else if (Carbon::parse($day) >= Carbon::parse($min_max_of_workingHours->max_date)) {
                $start = $min_max_of_workingHours->max_date;
                $end = null;
            } else {
                $start = $min_max_of_workingHours->min_date;
                $end = $min_max_of_workingHours->max_date;
            }
        }

        try {
            $working_hour = WorkingHour::where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->where('day', $index)
                ->where(function ($query) use ($start, $end, $min_max_of_workingHours, $day) {
                    if (!is_null($min_max_of_workingHours)) {
                        $query->whereDate('start_date', '>=', $start);
                        $query->whereDate('start_date', '<=', $day);
                        if (!is_null($end)) {
                            $query->whereDate('start_date', '<', $end);
                        }
                    }
                })
                ->selectRaw('min(time) as time')
                ->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $working_hour;
    }

    /**
     * get patients count when clinic work with queue
     * @param $upcoming_reservation
     * @return bool
     */
    public function getPatientsCount($upcoming_reservation)
    {
        try {
            $patients_approved = Reservation::where('day', $upcoming_reservation->day)
                ->where('status', ApiController::STATUS_APPROVED)
                ->where('user_id', '<>', $upcoming_reservation->user_id)
                ->where('clinic_id', $upcoming_reservation->clinic_id)
                ->where('queue', '<', $upcoming_reservation->queue)
                ->get();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $patients_approved->count();
    }

    /**
     * @param $status
     * @param $day
     * @param $time
     * @param $estimated_time
     * @return mixed
     */
    public function getUpcomingReservationFormatted($status, $day, $time, $estimated_time)
    {
        $formatted = new \stdClass();

        try {
            $formatted->day = self::getDateByFormat($day, 'd');
            $formatted->month = self::getDateByFormat($day, 'M');
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        if ($status == ApiController::STATUS_PENDING) {
            $formatted->time = self::getDateByFormat($time, 'g:i');
            $formatted->time_range = trans('lang.' . self::getDateByFormat($time, 'A'));

            $formatted->estimated_time = '';
            $formatted->estimated_time_range = '';
        } else {
            try {
                $formatted->time = self::getDateByFormat($time, 'g:i');
                $formatted->time_range = trans('lang.' . self::getDateByFormat($time, 'A'));
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
            try {
                $formatted->estimated_time = self::getDateByFormat($estimated_time, 'g:i');
                $formatted->estimated_time_range = trans('lang.' . self::getDateByFormat($estimated_time, 'A'));
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
        }
        return $formatted;
    }

    /**
     * @param $day
     * @param $time
     * @return mixed
     */
    public function getPastReservationFormatted($day, $time)
    {
        $formatted = new \stdClass();

        try {
            $formatted->day = self::getDateByFormat($day, 'd');
            $formatted->month = self::getDateByFormat($day, 'M');
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        $formatted->time = self::getDateByFormat($time, 'g:i');
        $formatted->time_range = trans('lang.' . self::getDateByFormat($time, 'A'));

        return $formatted;
    }


    /**
     * get first reservation attended
     * @param $upcoming_reservation
     * @return bool
     */
    public function getFirstReservationAttended($upcoming_reservation)
    {
        return Reservation::where('day', $upcoming_reservation->day)
            ->whereNotIn('reservations.status', [ApiController::STATUS_PENDING, ApiController::STATUS_CANCELED, ApiController::STATUS_MISSED])
            ->where('reservations.check_in', '!=', '00:00:00')
            ->where('reservations.clinic_id', $upcoming_reservation->clinic_id)
            ->whereRaw('reservations.queue = (select min(queue))')
            ->first();
    }

    /**
     * get all attended reservation before me
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getAttendedPatientsBeforeThisPatient($upcoming_reservation)
    {
        return Reservation::where('day', $upcoming_reservation->day)
            ->where('status', ApiController::STATUS_ATTENDED)
            ->where('check_in', '!=', '00:00:00')
            ->where('check_out', '!=', '00:00:00')
            ->where('user_id', '<>', $upcoming_reservation->user_id)
            ->where('clinic_id', $upcoming_reservation->clinic_id)
            ->where('queue', '<', $upcoming_reservation->queue)
            ->get();

    }

    /**
     * get all attended reservation before me if clinic work queue
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getAttendedReservationAndFirstReservation($upcoming_reservation)
    {
        //get first reservation
        try {
            $first_reservation = $this->getFirstReservationAttended($upcoming_reservation);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        //get all attended reservation before me
        try {
            $patients_attended = $this->getAttendedPatientsBeforeThisPatient($upcoming_reservation);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        $patients_attended_and_first_reservation = new \stdClass();
        $patients_attended_and_first_reservation->first_reservation = $first_reservation;
        $patients_attended_and_first_reservation->patients_attended = $patients_attended;
        $patients_attended_and_first_reservation->patients_attended_count = $patients_attended->count();

        return $patients_attended_and_first_reservation;
    }

    /**
     * get upcoming estimated time when clinic start
     * @param $patients_attended
     * @param $first_reservation_check_in
     * @param $patients_approved_count
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getUpcomingReservationEstimatedTimeAfterClinicStart($patients_attended, $first_reservation_check_in, $patients_approved_count, $upcoming_reservation)
    {
        $reservation_actual_time = 0;
        // get estimated time after started
        foreach ($patients_attended as $item) {
            try {
                $check_out = self::parseDate($item->check_out);
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
            try {
                $check_in = self::parseDate($item->check_in);
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
            $reservation_actual_time += $check_out->diffInMinutes($check_in);
        }
        $first_reservation_check_in = self::parseDate($first_reservation_check_in);

        $approved_patients_times_visit_time = ($patients_approved_count * $upcoming_reservation->avg_reservation_time) + ($reservation_actual_time);
        $reservations_time = ($first_reservation_check_in->addMinute($approved_patients_times_visit_time));
        try {
            $reservations_time = self::getTimeByFormat($reservations_time, 'h:i A');
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $reservations_time;
    }

    /**
     * get upcoming estimated time before clinic start
     * @param $patients_approved_count
     * @param $clinic_start_time
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_approved_count = 0, $clinic_start_time, $upcoming_reservation)
    {
        // get estimated time before started
        // if no attended patients ahead of me so i am the first so time will be clinic start time
        if ($upcoming_reservation->pattern == ApiController::PATTERN_INTERVAL) {
            try {
                $reservations_time = self::getTimeByFormat($upcoming_reservation->time, 'h:i A');
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
        } else {

            $avg_time_before_you = ($patients_approved_count) * ($upcoming_reservation->avg_reservation_time);
            try {
                $time = self::parseDate($clinic_start_time);
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
            $reservations_time = $time->addMinutes($avg_time_before_you);
            try {
                $reservations_time = self::getTimeByFormat($reservations_time, 'h:i A');
            } catch (\Exception $e) {
                return ApiController::catchExceptions($e->getMessage());
            }
        }

        return $reservations_time;
    }


    /**
     * to hide some data
     * @param $upcoming_reservation
     * @param $formatted
     * @return mixed
     */
    public function setUpcomingReservationData($upcoming_reservation, $formatted)
    {
        $returned_upcoming_reservation = new \stdClass();

        $returned_upcoming_reservation->id = $upcoming_reservation->id;
        $returned_upcoming_reservation->status = $upcoming_reservation->status;
        $returned_upcoming_reservation->clinic_name = $upcoming_reservation->clinic_name;
        $returned_upcoming_reservation->lng = $upcoming_reservation->lng;
        $returned_upcoming_reservation->lat = $upcoming_reservation->lat;
        $returned_upcoming_reservation->payment_method = $upcoming_reservation->payment_method;
        $returned_upcoming_reservation->transaction_id = $upcoming_reservation->transaction_id;
        $returned_upcoming_reservation->cashback_status = $upcoming_reservation->cashback_status;
        $returned_upcoming_reservation->number = $upcoming_reservation->number;
        $returned_upcoming_reservation->day = (app()->getLocale() == 'en') ? $formatted->day : self::enToAr($formatted->day);

        $returned_upcoming_reservation->month = (app()->getLocale() == 'en') ? $formatted->month : \Config::get('months.' . $formatted->month);
        $returned_upcoming_reservation->time = $formatted->time;
        $returned_upcoming_reservation->time_range = $formatted->time_range;

        // in case of past reservations dont return this
        if (isset($formatted->estimated_time)) {
            $returned_upcoming_reservation->serving_number = $upcoming_reservation->serving_number;

            $returned_upcoming_reservation->estimated_time = $formatted->estimated_time;
            $returned_upcoming_reservation->estimated_time_range = $formatted->estimated_time_range;
            $reservation_with_doctor = self::getReservationWithDoctor($upcoming_reservation);
            $returned_upcoming_reservation->is_in_session = ($reservation_with_doctor === false || $upcoming_reservation->status == ApiController::STATUS_MISSED) ? ApiController::FALSE : ApiController::TRUE;
            // get reservation queue status
            $returned_upcoming_reservation->queue_status = $upcoming_reservation->queue_status;
        }

        $offer = (new OfferRepository())->ApiGetOfferById($upcoming_reservation->offer_id);

        // reservation Fees
        $returned_upcoming_reservation->offer = ($upcoming_reservation->offer_id) != null ? ($offer ? $offer->price : 0) : 0;

        return $returned_upcoming_reservation;
    }

    /**
     * get user who create reservation
     * @param $reservation_created_by
     * @return mixed
     */
    public function getReservationCreatedByUser($reservation_created_by)
    {
        return User::where('id', $reservation_created_by)->first();
    }

    /**
     * get reservation fees ( subtotal_fees, VAT , TotalFees)
     * @param $services
     * @param $clinic_id
     * @param $type (check_up , follow_up)
     * @param $offer_id
     * @param null $user
     * @return mixed
     */
    public function getReservationFees($services, $clinic_id, $type, $offer_id, $user = null)
    {
        $this->getFees = new GetReservationFees();

        try {
            $clinic = Clinic::find($clinic_id);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        if ($offer_id != null) {
            $offer = (new OfferRepository())->getOfferById($offer_id);
        } else {
            $offer = null;
        }

        //  get reservation Doctor
        $doctor = User::where('account_id', $clinic->account_id)->where('role_id', 1)->first();
        if (!$doctor) {
            return ApiController::catchExceptions('Doctor not found');
        }

        // the reservation user
        if (!is_object($user) || !($user instanceof User)) {
            $user = (new UserRepository())->getUserById($user);
        }

        $reservation_services = [];

        if (is_string($services)) {
            $services = json_decode($services);
        }

        if ($services && count($services) > 0) {
            $isBothPremium = $user->is_premium && $doctor->is_premium;
            foreach ($services as $service) {
                // get the service if exists
                $doctor_service = DB::table('account_service')
                    ->join('services', 'services.id', 'account_service.service_id')
                    ->where('account_service.id', $service)
                    ->select('services.' . app()->getLocale() . '_name as name', 'premium_price', 'price')
                    ->first();
                if ($doctor_service) {
                    $reservation_services[] = [
                        'name' => $doctor_service->name,
                        'price' => $isBothPremium ? $doctor_service->premium_price : $doctor_service->price,
                    ];
                }
            }
        }

        // add services if exists
        $account_type = self::getAccountById($clinic->account_id)->type;
        $this->getFees->clinic = $clinic;
        $this->getFees->vat_included = $clinic->vat_included;
        $this->getFees->account_type = $account_type;
        $this->getFees->offer = $offer;
        $this->getFees->doctor = $doctor;
        $this->getFees->services = $reservation_services;
        $this->getFees->user = $user;
        $this->getFees->fees = ($type == ApiController::TYPE_CHECK_UP) ? $clinic->fees : $clinic->follow_up_fees;
        $this->getFees->premium_fees = ($type == ApiController::TYPE_CHECK_UP) ? $clinic->premium_fees : $clinic->premium_follow_up_fees;

        return $this->getFees->getResult();
    }


    /**
     *  get reservation fees ( subtotal_fees, Discount , TotalFees) after reservation and for history
     *
     * @param $reservation_id
     * @return mixed
     */
    public function getReservationFeesAfterReservation($reservation_id)
    {
        try {
            $payment = (new ReservationsPayment())->where('reservation_id', $reservation_id)->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return (new GetReservationFeesAfterReservation($payment))->getResult();
    }

    /**
     * cancel reservation when patient remove doctor from list
     * @param $reservation_id
     * @return mixed
     * @throws \Exception
     */
    public function cancelReservation($reservation_id)
    {
        $reservation = $this->getReservationById($reservation_id);
        if (!$reservation) {
            return false;
        }
        DB::beginTransaction();
        try {
            $reservation->status = ApiController::STATUS_CANCELED;
            $reservation->update();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::commit();
    }

    /**
     * get upcoming reservation for this patient with this doctor
     * @param $user_id
     * @param $account_id
     * @return mixed
     */
    public function getUpcomingReservationWithThisDoctor($user_id, $account_id)
    {
        $doctor = (new DoctorRepository)->getDoctorByAccountId($account_id);
        if (!$doctor) {
            return false;
        }
        try {
            return Reservation::join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->where('clinics.created_by', $doctor->id)
                ->where('reservations.user_id', $user_id)
                ->where('reservations.day', '>=', self::getDateByFormat(self::getToday(), 'Y-m-d'))
                ->where('reservations.status', ApiController::STATUS_APPROVED)
                ->select('reservations.id')
                ->orderBy('reservations.updated_at', 'desc')
                ->first();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    /**
     * get all approved reservation today
     * @param $clinic_id
     * @return mixed
     */
    public function getApprovedReservationsCountToday($clinic_id)
    {
        try {
            return Reservation::where('day', self::getDateByFormat(self::getToday(), 'Y-m-d'))
                ->where('status', ApiController::STATUS_APPROVED)
                ->where('clinic_id', $clinic_id)
                ->count();
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

    }

    /**
     * get past reservation details
     * @param $reservation_id
     * @param $user
     * @param $reservationController
     * @return mixed
     */
    public function getPastReservationDetails($reservation_id, $user, ReservationController $reservationController)
    {
        $reservation_details = new \stdClass();
        try {
            // first step get the reservation fees
            $reservation = $this->checkIfReservationPastForReservationDetails($user->id, $reservation_id);

            $clinic = (new ClinicRepository())->getClinicById($reservation->clinic_id);

            $reservation_details->fees = $this->getReservationFeesAfterReservation($reservation->id);
            $reservation_details->day = $reservation->day;

            //call getPatientsCount to get patients count before me and clinic start time
            $account_type = self::getAccountById($clinic->account_id);
            if (!$account_type) {
                return false;
            }

            //call getPatientsCount to get patients count before me and clinic start time
            $patients_count_and_clinic_start_time = $reservationController->getPatientsCount($reservation);
            if ($patients_count_and_clinic_start_time === false) {
                return false;
            }

            $time = $this->getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_count_and_clinic_start_time->patients_approved_count,
                $patients_count_and_clinic_start_time->clinic_start_time, $reservation);
            if ($time === false) {
                return false;
            }

            $estimated_time = $reservationController->getUpcomingReservationEstimatedTime($reservation);
            if ($estimated_time === false) {
                return false;
            }

            $estimated_time = ($account_type->type == ApiController::ACCOUNT_TYPE_POLY) ? $time : $estimated_time;

            $reservation_details->time = DateTrait::getDateByFormat($estimated_time, 'g:i');
            $reservation_details->time_range = trans('lang.' . DateTrait::getDateByFormat($estimated_time, 'A'));
            $doctor = User::join('accounts', 'accounts.id', 'users.account_id')->where('users.account_id', $clinic->account_id)->where('users.role_id', 1)->select('users.id', 'users.unique_id', 'users.image', 'users.name', 'accounts.type')->first();
            $reservation_details->doctor = $doctor;

            if ($doctor->type == ApiController::ACCOUNT_TYPE_SINGLE) {
                // single
                $reservation_details->clinic_name = $clinic->province ? (app()->getLocale() == 'en' ? $clinic->province->{'en_name'} . ' branch' : $clinic->province->{'ar_name'} . ' فرع') : '';
                $reservation_details->address = $clinic->{app()->getLocale() . '_address'};
            } else {
                // poly
                $reservation_details->clinic_name = $clinic->{app()->getLocale() . '_name'};
                $reservation_details->address = $clinic->account->{app()->getLocale() . '_address'};
            }

            // queue number
            $reservation_details->number = $reservation->queue;
            $queue = $this->getQueueToday($reservation->clinic_id);
            // get clinic queue status

            if ($queue) {
                $reservation_details->queue_status = $queue->queue_status;
            } else {
                $reservation_details->queue_status = 1;
            }
            // dont close queue if reservation is not in the same day
            if ($reservation_details->day != now()->format('Y-m-d')) {
                $reservation_details->queue_status = 1;
            }


            // lat and lng
            $reservation_details->lat = $clinic->lat;
            $reservation_details->lng = $clinic->lng;

            // clinic pattern
            $reservation_details->clinic_pattern = $clinic->pattern;
            $reservation_details->clinic_mobile = $clinic->mobile;
            $reservation_details->account_type = $account_type->type;

            $dayIndex = DateTrait::getDayIndex($reservation->day);
            // from and to times
            $min_max_of_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, $dayIndex, $reservation_details->day);

            if ($min_max_of_workingHours) {
                $reservation_details->clinic_start = self::getDateByFormat($min_max_of_workingHours->min_time, 'g:i');
                $reservation_details->clinic_start_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->min_time, 'A'));
                $reservation_details->clinic_end = self::getDateByFormat($min_max_of_workingHours->max_time, 'g:i');
                $reservation_details->clinic_end_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->max_time, 'A'));
            } else {
                $reservation_details->clinic_start = null;
                $reservation_details->clinic_start_range = null;
                $reservation_details->clinic_end = null;
                $reservation_details->clinic_end_range = null;
            }


            if ($clinic->pattern == ApiController::PATTERN_QUEUE) {
                // get break times
                $reservation_details->clinic_break = (new WorkingHourRepository())->getArrayOfBreakTimesInQueue($clinic->id, $dayIndex);
            }

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $reservation_details;
    }


    /**
     * get upcoming reservation details
     * @param $reservation_id
     * @param $user
     * @param $reservationController
     * @return mixed
     */
    public function getUpcomingReservationDetails($reservation_id, $user, ReservationController $reservationController)
    {
        $reservation_details = new \stdClass();
        try {
            // first step get the reservation fees
            $reservation = $this->checkIfReservationUpcomingForReservationDetails($user->id, $reservation_id);

            $clinic = (new ClinicRepository())->getClinicById($reservation->clinic_id);

            $reservation_details->fees = $this->getReservationFeesAfterReservation($reservation->id);
            $reservation_details->day = $reservation->day;

            //call getPatientsCount to get patients count before me and clinic start time
            $account_type = self::getAccountById($clinic->account_id);
            if (!$account_type) {
                return false;
            }

            //call getPatientsCount to get patients count before me and clinic start time
            $patients_count_and_clinic_start_time = $reservationController->getPatientsCount($reservation);
            if ($patients_count_and_clinic_start_time === false) {
                return false;
            }

            $time = $this->getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_count_and_clinic_start_time->patients_approved_count,
                $patients_count_and_clinic_start_time->clinic_start_time, $reservation);
            if ($time === false) {
                return false;
            }

            $estimated_time = $reservationController->getUpcomingReservationEstimatedTime($reservation);
            if ($estimated_time === false) {
                return false;
            }

            $estimated_time = ($account_type->type == ApiController::ACCOUNT_TYPE_POLY) ? $time : $estimated_time;

            $reservation_details->time = DateTrait::getDateByFormat($estimated_time, 'g:i');
            $reservation_details->time_range = trans('lang.' . DateTrait::getDateByFormat($estimated_time, 'A'));
            $doctor = User::join('accounts', 'accounts.id', 'users.account_id')->where('users.account_id', $clinic->account_id)->where('users.role_id', 1)->select('users.id', 'users.unique_id', 'users.image', 'users.name', 'accounts.type')->first();
            $reservation_details->doctor = $doctor;

            if ($doctor->type == ApiController::ACCOUNT_TYPE_SINGLE) {
                // single
                $reservation_details->clinic_name = $clinic->province ? (app()->getLocale() == 'en' ? $clinic->province->{'en_name'} . ' branch' : $clinic->province->{'ar_name'} . ' فرع') : '';
                $reservation_details->address = $clinic->{app()->getLocale() . '_address'};
            } else {
                // poly
                $reservation_details->clinic_name = $clinic->{app()->getLocale() . '_name'};
                $reservation_details->address = $clinic->account->{app()->getLocale() . '_address'};
            }

            // queue number
            $reservation_details->number = $reservation->queue;
            $queue = $this->getQueueToday($reservation->clinic_id);
            // get clinic queue status

            if ($queue) {
                $reservation_details->queue_status = $queue->queue_status;
            } else {
                $reservation_details->queue_status = 1;
            }
            // dont close queue if reservation is not in the same day
            if ($reservation_details->day != now()->format('Y-m-d')) {
                $reservation_details->queue_status = 1;
            }

            $reservation_details->serving_number = ($queue && $reservation_details->day == self::getDateByFormat(self::getToday(), 'Y-m-d')) ? $queue->queue : 0;

            // lat and lng
            $reservation_details->lat = $clinic->lat;
            $reservation_details->lng = $clinic->lng;

            // clinic pattern
            $reservation_details->clinic_pattern = $clinic->pattern;
            $reservation_details->clinic_mobile = $clinic->mobile;
            $reservation_details->account_type = $account_type->type;

            $dayIndex = DateTrait::getDayIndex($reservation->day);
            // from and to times
            $min_max_of_workingHours = (new WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id, $dayIndex, $reservation_details->day);

            if ($min_max_of_workingHours) {
                $reservation_details->clinic_start = self::getDateByFormat($min_max_of_workingHours->min_time, 'g:i');
                $reservation_details->clinic_start_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->min_time, 'A'));
                $reservation_details->clinic_end = self::getDateByFormat($min_max_of_workingHours->max_time, 'g:i');
                $reservation_details->clinic_end_range = trans('lang.' . self::getDateByFormat($min_max_of_workingHours->max_time, 'A'));
            } else {
                $reservation_details->clinic_start = null;
                $reservation_details->clinic_start_range = null;
                $reservation_details->clinic_end = null;
                $reservation_details->clinic_end_range = null;
            }


            if ($clinic->pattern == ApiController::PATTERN_QUEUE) {
                // get break times
                $reservation_details->clinic_break = (new WorkingHourRepository())->getArrayOfBreakTimesInQueue($clinic->id, $dayIndex);
            }

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $reservation_details;
    }

    /**
     * get clinic queue if start
     * @param $reservation
     * @return mixed
     */
    public function getClinicQueueToday($reservation)
    {
        return ClinicQueue::where('clinic_id', $reservation->clinic_id)->today()->first();
    }

    /**
     * get clinic queue if start
     * @param $reservation
     * @return mixed
     */
    public function getReservationWithDoctor($reservation)
    {
        try {
            $reservation_with_doctor = ClinicQueue::where('clinic_id', $reservation->clinic_id)->where('queue', $reservation->queue)->today()->first();
            if (!$reservation_with_doctor) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return true;
    }

    public function setReservationDataForManger($upcoming_reservation, $formatted)
    {
        $returned_upcoming_reservation = new \stdClass();

        $returned_upcoming_reservation->id = $upcoming_reservation->id;
        $returned_upcoming_reservation->status = $upcoming_reservation->status;
        $returned_upcoming_reservation->clinic_name = $upcoming_reservation->clinic_name;
        $returned_upcoming_reservation->name = $upcoming_reservation->name;
        $returned_upcoming_reservation->image = $upcoming_reservation->image;
        $returned_upcoming_reservation->unique_id = $upcoming_reservation->unique_id;
        $returned_upcoming_reservation->payment_method = $upcoming_reservation->payment_method;
        $returned_upcoming_reservation->number = $upcoming_reservation->number;
        $returned_upcoming_reservation->serving_number = $upcoming_reservation->serving_number;
        $returned_upcoming_reservation->day = $formatted->day;
        $returned_upcoming_reservation->month = $formatted->month;
        $returned_upcoming_reservation->time = $formatted->time;
        $returned_upcoming_reservation->time_range = $formatted->time_range;
        $returned_upcoming_reservation->estimated_time = $formatted->estimated_time;
        $returned_upcoming_reservation->estimated_time_range = $formatted->estimated_time_range;

        if (isset($upcoming_reservation->address)) {
            $returned_upcoming_reservation->address = $upcoming_reservation->address;
        }

        return $returned_upcoming_reservation;

    }

    public function changeReservationStatus($reservation, $status)
    {
        $reservation->update([
            'status' => $status
        ]);

        return $reservation;
    }


    /**
     * get all days that have reservations
     * @param $request
     * @param $doctor
     * @return mixed
     */
    public function getUpcomingReservationsDays($request, $doctor)
    {
        $user = auth()->guard('api')->user();

        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;
        try {
            return Reservation::leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('accounts', 'accounts.id', 'clinics.account_id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->where(function ($query) use ($user, $doctor, $request) {
                    if ($user->role_id == ApiController::ROLE_ASSISTANT) {
                        $query->where('reservations.clinic_id', $user->clinic_id);
                    } elseif ($user->role_id == ApiController::ROLE_DOCTOR && $request->clinic_id != 0) {
                        $query->where('reservations.clinic_id', $request->clinic_id);
                    } else {
                        $query->whereIn('reservations.clinic_id', (new PatientRepository)->getClinicsRelatedToDoctor($doctor->id));
                    }
                })
                ->where('working_hours.is_break', 0)
                ->whereIn('reservations.clinic_id', (new PatientRepository)->getClinicsRelatedToDoctor($doctor->id))
                ->where('reservations.day', '>=', self::getDateByFormat(self::getToday(), 'Y-m-d'))
                ->whereIn('reservations.status', [ApiController::STATUS_APPROVED, ApiController::STATUS_MISSED])
                ->select('reservations.day')
                ->distinct('reservations.day')
                ->orderBy('reservations.day', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * add reservations to specific day
     * @param $request
     * @param $doctor
     * @return bool
     */
    public function addReservationsToSpecificDay($request, $doctor)
    {
        $user = auth()->guard('api')->user();

        $reservation_days = $this->getUpcomingReservationsDays($request, $doctor);
        if ($reservation_days === false) {
            return false;
        }

        if ($reservation_days->count() > 0) {
            try {
                // in case of single doctor
                if ($user->account->type == 0) {
                    foreach ($reservation_days as $day) {
                        $reservations = Reservation::leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                            ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                            ->join('accounts', 'accounts.id', 'clinics.account_id')
                            ->join('users', 'reservations.user_id', 'users.id')
                            ->whereIn('reservations.status', [ApiController::STATUS_APPROVED, ApiController::STATUS_MISSED])
                            ->where(function ($query) use ($user, $doctor, $request) {
                                if ($user->role_id == ApiController::ROLE_ASSISTANT) {
                                    $query->where('reservations.clinic_id', $user->clinic_id);
                                } elseif ($user->role_id == ApiController::ROLE_DOCTOR && $request->clinic_id != 0) {
                                    $query->where('reservations.clinic_id', $request->clinic_id);
                                } else {
                                    $query->whereIn('reservations.clinic_id', (new PatientRepository)->getClinicsRelatedToDoctor($doctor->id));
                                }
                            })->where('reservations.day', $day->day)
                            ->where('working_hours.is_break', 0)
                            ->select('accounts.type as account_type', 'users.name', 'users.unique_id', 'reservations.payment_method'
                                , DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image),IF((LOCATE("facebook",users.image,1) != 0) OR (LOCATE("google",users.image,1) != 0),users.image,CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image))) as image')
                                , DB::raw('DATE_FORMAT(working_hours.time, "%h:%i %p") as time'), 'reservations.id',
                                'reservations.status', 'reservations.queue', 'clinics.id as clinic_id', 'clinics.pattern', 'accounts.' . app()->getLocale() . '_name as clinic_name', 'clinics.' . app()->getLocale() . '_address as address', 'users.is_premium')
                            ->orderBy('reservations.day', 'asc')
                            ->get();

                        if ($reservations->count() > 0) {
                            // add reservations to day
                            $day->reservations = $reservations;
                            // check if today or tomorrow
                            $day->day = self::getDayName($day->day);
                        }
                    }
                } else {
                    // in case of poly
                    // loop on days and get it's reservations
                    foreach ($reservation_days as $day) {
                        $reservations = Reservation::leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                            ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                            ->join('specialities', 'clinics.speciality_id', 'specialities.id')
                            ->join('accounts', 'accounts.id', 'clinics.account_id')
                            ->join('users', 'reservations.user_id', 'users.id')
                            ->whereIn('reservations.status', [ApiController::STATUS_APPROVED, ApiController::STATUS_MISSED])
                            ->where(function ($query) use ($user, $doctor, $request) {
                                if ($user->role_id == ApiController::ROLE_ASSISTANT) {
                                    $query->where('reservations.clinic_id', $user->clinic_id);
                                } elseif ($user->role_id == ApiController::ROLE_DOCTOR && $request->clinic_id != 0) {
                                    $query->where('reservations.clinic_id', $request->clinic_id);
                                } else {
                                    $query->whereIn('reservations.clinic_id', (new PatientRepository)->getClinicsRelatedToDoctor($doctor->id));
                                }
                            })->where('reservations.day', $day->day)
                            ->where('working_hours.is_break', 0)
                            ->select('accounts.type as account_type', 'users.name', 'users.unique_id', 'specialities.' . app()->getLocale() . '_speciality As speciality', 'reservations.payment_method'
                                , DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image),IF((LOCATE("facebook",users.image,1) != 0) OR (LOCATE("google",users.image,1) != 0),users.image,CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image))) as image')
                                , DB::raw('DATE_FORMAT(working_hours.time, "%h:%i %p") as time'), 'reservations.id',
                                'reservations.status', 'reservations.queue', 'clinics.id as clinic_id', 'clinics.pattern', 'accounts.' . app()->getLocale() . '_name as clinic_name', 'clinics.' . app()->getLocale() . '_address as address', 'users.is_premium')
                            ->orderBy('reservations.day', 'asc')
                            ->get();


                        if ($reservations->count() > 0) {
                            // add reservations to day
                            $day->reservations = $reservations;
                            // check if today or tomorrow
                            $day->day = self::getDayName($day->day);
                        }
                    }
                }


            } catch
            (\Exception $e) {
                \Log::info($e->getMessage());
                return false;
            }
            return $reservation_days;
        }
    }

    /**
     * check if this day is holiday
     * @param $day
     * @param $reservation
     * @return mixed
     */
    public function checkIfHoliday($day, $reservation)
    {
        try {
            $holiday = Holiday::where('day', $day)
                ->where('clinic_id', $reservation->clinic_id)
                ->first();
            if (!$holiday) {
                return false;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }


    /**
     * check if patient have reservation with this doctor today and he can not add another if he has reservation approved or missed
     * @param $clinic_id
     * @param $user_id
     * @param $day
     * @return mixed
     */
    public function checkIfPatientReserveWithThisDoctorTwiceAtDayWhenAdd($clinic_id, $user_id, $day)
    {
        $requested_clinic = (new ClinicRepository)->getClinicById($clinic_id);
        if ($requested_clinic === false) {
            return false;
        }
        $clinics_ids_related_to_same_account = ((new ClinicRepository)->getClinicsRelatedToSameAccount($requested_clinic->account_id))->pluck('id');
        if ($requested_clinic === false) {
            return false;
        }
        try {
            $patient_reservation_with_doctor = Reservation::where('reservations.user_id', $user_id)
                ->whereIn('reservations.clinic_id', $clinics_ids_related_to_same_account)
                ->whereIn('reservations.status', [ApiController::STATUS_APPROVED, ApiController::STATUS_MISSED])
                ->where('day', $day)
                ->get();
            if (count($patient_reservation_with_doctor) < 0) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        if (count($patient_reservation_with_doctor) == 2) {
            return true;
        }
        return false;
    }

    /**
     * check if patient have reservation with this doctor today and he can not reschedule it if he has reservation attended or canceled
     * @param $clinic_id
     * @param $user_id
     * @param $day
     * @return mixed
     */
    public function checkIfPatientReserveWithThisDoctorTwiceAtDayWhenReschedule($clinic_id, $user_id, $day)
    {
        $requested_clinic = (new ClinicRepository)->getClinicById($clinic_id);
        if ($requested_clinic === false) {
            return false;
        }
        $clinics_ids_related_to_same_account = ((new ClinicRepository)->getClinicsRelatedToSameAccount($requested_clinic->account_id))->pluck('id');
        if ($requested_clinic === false) {
            return false;
        }
        try {
            $patient_reservation_with_doctor = Reservation::where('reservations.user_id', $user_id)
                ->whereIn('reservations.clinic_id', $clinics_ids_related_to_same_account)
                ->whereIn('reservations.status', [ApiController::STATUS_ATTENDED])
                ->where('day', $day)
                ->first();
            if (!$patient_reservation_with_doctor) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return true;
    }

    /**
     * edit reservation
     * @param $request
     * @param $user
     * @return mixed
     * @throws \Exception
     */
    public function editReservation($request, $user)
    {
        try {
            $reservation = Reservation::find($request->reservation_id);
            if (!$reservation) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        DB::beginTransaction();
        try {
            $reservation->update($request->all());
            $reservation->status = 1;
            $reservation->updated_by = $user->id;
            $reservation->update();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::catchExceptions($e->getMessage());
        }
        DB::commit();

        return $reservation;
    }

    private function getPatientsCountForReservationDetails($upcoming_reservation)
    {
        $patients_count_and_clinic_start_time = new \stdClass();

        //when queue doesn't start yet
        //get all reservation before me if clinic work intervals and it's time smaller than may time
        try {
            $patients_approved_count = $this->getPatientsCount($upcoming_reservation);
        } catch (\Exception $e) {
            return false;
        }
        //  get index day from days list in config
        try {
            $index = self::getDayIndex($upcoming_reservation->day);
        } catch (\Exception $e) {
            return false;
        }

        // get clinic start time
        $working_hour = $this->getClinicStartTime($upcoming_reservation->clinic_id, $index, $upcoming_reservation->day);

        if ($working_hour === false) {
            return false;
        }

        $patients_count_and_clinic_start_time->patients_approved_count = $patients_approved_count;
        $patients_count_and_clinic_start_time->clinic_start_time = $working_hour->time;

        return $patients_count_and_clinic_start_time;
    }


    public function checkIfReservationUpcomingForReservationDetails($user_id, $reservation_id)
    {
        // if user have reservation upcoming
        try {
            return Reservation::leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('accounts', 'accounts.id', 'clinics.account_id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->where('reservations.id', $reservation_id)
                ->where('reservations.status', ApiController::STATUS_APPROVED)
                ->where('reservations.user_id', $user_id)
                ->where('reservations.day', '>=', self::getDateByFormat(self::getToday(), 'Y-m-d'))
                ->select(
                    'working_hours.time',
                    'reservations.id',
                    'reservations.status',
                    'reservations.user_id',
                    'reservations.day',
                    'reservations.offer_id',
                    'reservations.clinic_id',
                    'reservations.type',
                    'reservations.clinic_id',
                    'clinics.lng',
                    'clinics.lat',
                    'clinics.pattern',
                    'reservations.queue',
                    'clinics.avg_reservation_time',
                    'accounts.' . app()->getLocale() . '_name as clinic_name'
                )
                ->orderBy('reservations.day', 'asc')
                ->first();

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }


    public function checkIfReservationPastForReservationDetails($user_id, $reservation_id)
    {
        // if user have reservation upcoming
        try {
            return Reservation::leftJoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('accounts', 'accounts.id', 'clinics.account_id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->where('reservations.id', $reservation_id)
                ->where('reservations.status', '!=', ApiController::STATUS_APPROVED)
                ->where('reservations.user_id', $user_id)
                ->select(
                    'working_hours.time',
                    'reservations.id',
                    'reservations.status',
                    'reservations.user_id',
                    'reservations.day',
                    'reservations.offer_id',
                    'reservations.clinic_id',
                    'reservations.type',
                    'reservations.clinic_id',
                    'clinics.lng',
                    'clinics.lat',
                    'clinics.pattern',
                    'reservations.queue',
                    'clinics.avg_reservation_time',
                    'accounts.' . app()->getLocale() . '_name as clinic_name'
                )
                ->orderBy('reservations.day', 'asc')
                ->first();

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }

    /**
     *  update reservation column
     *
     * @param $reservation_id
     * @param $column_name
     * @param $column_value
     * @return bool
     */
    public function updateReservaionColumn($reservation_id, $column_name, $column_value)
    {
        try {
            return Reservation::where('id', $reservation_id)->update([
                $column_name => $column_value
            ]);

        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
    }


    /**
     *  check if the promo-code is not used by user or used (if not used return false)
     *
     * @param $patient_id
     * @param $code_id
     * @return bool
     */
    public function getReservationByPromoCodeAndPatinet($patient_id, $code_id)
    {
        try {
            $reservation = Reservation::where('user_id', $patient_id)->where('promo_code_id', $code_id)->first();
            if ($reservation) {
                return true;
            }
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return true;
        }

        return false;
    }
}
