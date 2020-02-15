<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Controllers\Admin\ReservationsController;
use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\ReservationInterface;
use App\Http\Traits\DateTrait;
use App\Models\Account;
use App\Models\Clinic;
use App\Models\Reservation;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Database\Eloquent\Collection;

class ReservationRepository extends ParentRepository implements ReservationInterface
{
    public $reservation;
    use DateTrait;

    public function __construct()
    {
        $this->reservation = new Reservation();
    }

    /**
     *  get first clinic reservations in the given day
     *
     * @param $clinic_id
     * @param $day
     * @return mixed
     */
    public function getFirstReservationByClinicAndDayOrdered($clinic_id, $day)
    {
        try {
            return $this->reservation
                ->where('clinic_id', $clinic_id)
                ->whereIn('status', [ReservationsController::R_STATUS_APPROVED, ReservationsController::R_STATUS_ATTENDED])// attended and approved
                ->where('day', $day)
                ->orderBy('queue', 'desc')->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * create new reservations
     *
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function createNewReservation($request)
    {
        try {
            return $this->reservation->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get reservation by id
     *
     * @param $id
     * @param array $status not required ( in case if ew want to add status)
     * @return mixed
     */
    public function getReservationById($id, $status = [])
    {
        try {
            return $this->reservation
                ->where('id', $id)
                ->where(function ($query) use ($status) {
                    if (count($status) != 0) {
                        $query->whereIn('status', $status);
                    }
                })->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  check if user has reservation on this day and clinic
     *
     * @param $user_id
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function checkIfUserHasReservation($user_id, $day, $clinic_id)
    {
        try {
            $res = $this->reservation->where('user_id', $user_id)
                ->where('day', $day)
                ->where('clinic_id', $clinic_id)
                ->whereNotIn('status', [WebController::R_STATUS_CANCELED, WebController::R_STATUS_MISSED])
                ->first();
            if (empty($res)) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update reservations
     *
     * @param $reservation
     * @param $request
     * @return mixed
     */
    public function updateReservation($reservation, $request)
    {
        try {
            $reservation->update($request->all());
            return $reservation;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  set cash reservation to Paid
     *
     * @param $reservation
     * @param $auth_id
     * @return mixed
     */
    public function setReservationToPaid($reservation, $auth_id)
    {
        try {
            $reservation->update([
                'status' => 3,
                'transaction_id' => -2,
                'updated_by' => $auth_id
            ]);
            return $reservation;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  get one reservation by status and clinic_id and queue
     *
     * @param $status
     * @param string $clinic_id
     * @param string $queue
     * @param string $order
     * @return mixed
     * @throws \Exception
     */
    public function getReservationByStatusAndClinic($status = '', $clinic_id = '', $queue = '', $order = '')
    {
        try {
            return $this->reservation->where(function ($query) use ($queue, $clinic_id, $status, $order) {
                // in case we use $queue
                if ($queue != '') {
                    if (is_array($queue)) {
                        // in  case queue > number
                        // queue = queue
                        $query->where('queue', $queue[1], $queue[0]);
                    } else {
                        $query->where('queue', '>=', $queue);
                    }
                }
                // in case we use $clinic_id
                if ($clinic_id != '') {
                    $query->where('clinic_id', $clinic_id);
                }
                // in case we use $status
                if ($status != '') {
                    // in case multi status
                    if (is_array($status)) {
                        $query->whereIn('status', $status);
                    } else {
                        // in case one status
                        $query->where('status', $status);
                    }
                }
                // in case we use $queue
                if ($order != '') {
                    if (is_array($order)) {
                        // orderBy column'
                        // orderBy column' [desc,asc]
                        $query->orderBy($order[0], $order[1]);
                    } else {
                        $query->orderBy($order);
                    }
                }

            })->today()->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }

    }

    /**
     *  get one reservation by status and clinic_id and queue
     *
     * @param $status
     * @param string $clinic_id
     * @param string $queue
     * @param string $order
     * @return mixed
     */
    public function getAllReservationsByStatusAndClinic($status, $clinic_id = '', $queue = '', $order = '')
    {
        try {
            return $this->reservation->where(function ($query) use ($queue, $clinic_id, $status, $order) {
                // in case we use $queue
                if ($queue != '') {
                    if (is_array($queue)) {
                        $query->where('queue', $queue[1], $queue[0]);
                    } else {
                        $query->where('queue', $queue);
                    }
                }

                // in case we use $clinic_id
                if ($clinic_id != '') {
                    $query->where('clinic_id', $clinic_id);
                }
                // in case multi status
                if (is_array($status)) {
                    $query->whereIn('status', $status);
                } else {
                    // in case one status
                    $query->where('status', $status);
                }

                // in case we use $queue
                if ($order != '') {
                    $query->orderBy($order);
                }

            })->today()->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  change the reservation status after visit to attended or set it to missed
     *
     * @param $reservation
     * @param $status
     * @param $user_id
     * @return mixed
     */
    public function ChangeReservationStatusAfterVisit($reservation, $status, $user_id)
    {
        try {
            $reservation->update([
                'status' => $status,
                'check_out' => Carbon::now()->toTimeString(),
                'updated_by' => $user_id
            ]);

            return $reservation;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  add reservation check-in
     *
     * @param $reservation
     * @param $user_id
     * @param int $status
     * @return mixed
     */
    public function addReservationCheckInAndOut($reservation, $user_id, $status = 0)
    {

        try {
            if ($status == 0) {
                $field = 'check_in';
            } else {
                $field = 'check_out';
            }

            $reservation->update([
                $field => Carbon::now()->toTimeString(),
                'updated_by' => $user_id
            ]);

            return $reservation;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get collection of user reservations
     *
     * @param $user_id
     * @param array $status
     * @return mixed
     */
    public function getUSerReservationsByStatus($user_id, $status = [])
    {
        try {
            return $this->reservation->where('user_id', $user_id)
                ->whereIn('status', $status)
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $status
     * @param string $clinic_id
     * @param string $queue
     * @return mixed
     */
    public function getNextReservationInQueue($status, $clinic_id = '', $queue = '')
    {
        try {
            return $this->reservation->where('status', $status)
                ->where('clinic_id', $clinic_id)
                ->where('queue', '>', $queue)
                ->orderBy('queue')
                ->today()
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get count of all the reservations that is attended or approved
     *
     * @param string $clinic_id
     * @param string $day
     * @return mixed
     */
    public function getCountOfAllReservationsWhichIsApprovedAndAttended($clinic_id = '', $day = '')
    {
        try {
            return $this->reservation
                ->whereIn('status', [WebController::R_STATUS_ATTENDED, WebController::R_STATUS_APPROVED])
                ->where('clinic_id', $clinic_id)
                ->orderBy('queue')
                ->whereDate('day', '=', $day)
                ->count();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return -1;
        }
    }

    /**
     * @param $reservation
     * @param $status
     * @return mixed
     */
    public function changeReservationStatus($reservation, $status)
    {
        try {
            $reservation->update([
                'status' => $status
            ]);

            return $reservation;

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get the trashed working hours
     *
     * @param $trashedWorkingHours
     * @param $pattern
     * @param null $day
     * @param null $start_date
     * @param null $end_date
     * @return mixed
     */
    public function getReservationUsingTrashedWorkingHours($trashedWorkingHours, $pattern, $day = null, $start_date = null, $end_date = null)
    {
        try {
            return $this->reservation
                ->where('status', WebController::R_STATUS_APPROVED)
                ->where(function ($query) use ($day, $pattern, $trashedWorkingHours, $start_date, $end_date) {

                    // in case of Interval clinics
                    if ($pattern == 0) {
                        if (count($trashedWorkingHours) > 0) {
                            // in case of interval
                            $query->whereIn('working_hour_id', $trashedWorkingHours);
                        }
                    } else {
                        // in case of Queue
                        if (!is_null($day)) {
                            // in case of Queue pattern
                            $query->where('working_hour_id', null)
                                ->whereRaw('DATE_FORMAT(day,"' . '%W' . '")="' . Config::get('lists.days')[$day]['en_name'] . '"');
                            if (!empty($start_date)) {
                                $query->where('day', '>=', $start_date);
                            }
                            if (!empty($end_date)) {
                                $query->where('day', '<', $end_date);
                            }
                        }
                    }
                })->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }


    /**
     *
     *  get the number of reservation after today or certain date in specific clinic
     *
     * @param $clinic_id
     * @param $dayName
     * @param $start_date
     * @param $expiry_date
     * @return mixed
     */
    public function getReservationsCountInClinicAndDay($clinic_id, $dayName, $start_date, $expiry_date = null)
    {
        try {
            return $this->reservation
                ->where('clinic_id', $clinic_id)
                ->where('status', '=', '1')
                ->where(function ($query) use ($start_date, $expiry_date, $dayName) {

                    $query->whereIn(\DB::raw('DATE_FORMAT(day,"' . '%W' . '")'), $dayName);

                    if (is_null($start_date)) {
                        $query->whereDate('day', '>=', now()->format('Y-m-d'));
                    } else {
                        $query->whereDate('day', '>=', $start_date);
                    }

                    if (!is_null($expiry_date)) {
                        $query->whereDate('day', '<', $expiry_date);
                    }
                })
                ->count();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of reservation starting from particular day
     *
     * @param $clinic_id
     * @param $dayName
     * @param $start_date
     * @return Collection
     */
    public function getReservationsListInClinicAndDay($clinic_id, $dayName, $start_date)
    {

        try {
            return $this->reservation
                ->where('clinic_id', $clinic_id)
                ->whereRaw('DATE_FORMAT(day,"' . '%W' . '") IN "' . $dayName . '"')
                ->where('status', '=', '1')
                ->where(function ($query) use ($start_date) {
                    $query->whereDate('day', '>=', $start_date);
                })
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }


    /**
     *  get list of reservations with
     *
     * @param null $limit
     * @return Collection
     */
    public function getLastReservationsCountWithDoctors($limit = null)
    {
        try {
            return Account::join('users', 'users.account_id', 'accounts.id')->where('users.role_id', 1)
                ->leftJoin('clinics', 'clinics.account_id', 'accounts.id')
                ->leftJoin('reservations', 'reservations.clinic_id', 'clinics.id')
                ->select('accounts.id', 'users.id as user_id', 'accounts.' . app()->getLocale() . '_name as account_name', 'users.mobile', 'users.email', \DB::raw('count(reservations.id) as count'))
                ->where(function ($query) {
                    if (debug_mode() == true) {  // hide test data when debug mode is off
                        $query->whereNotIn('users.id', get_test_users('doctor'));
                    }
                })
                ->groupBy('accounts.id')
                ->orderBy('count', 'desc')
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  get list of reservations for Seena admin
     *
     * @param $account_id
     * @return Collection
     */
    public function getAccountReservations($account_id)
    {
        try {
            return $this->reservation
                ->whereIn('reservations.clinic_id',
                    Clinic::where('account_id', $account_id)->pluck('id')
                )->join('users', 'reservations.user_id', 'users.id')
                ->select('reservations.*', 'users.name', DB::raw('DATE_FORMAT(reservations.day, "%Y-%m-%d") as day'))
                ->orderBy('reservations.day', 'desc')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }
}
