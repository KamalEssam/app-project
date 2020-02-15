<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\WorkingHourInterface;
use App\Models\WorkingHour;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;

class WorkingHourRepository extends ParentRepository implements WorkingHourInterface
{
    private $workingHour;

    public function __construct()
    {
        $this->workingHour = new WorkingHour();
    }

    /**
     *  get working hours using day number and clinic id
     *
     * @param $day
     * @param $clinic_id
     * @param null $start_date
     * @return mixed
     */
    public function getWorkingHoursByDayAndClinicId($day, $clinic_id, $start_date = null)
    {
        try {
            return $this->workingHour->where('day', $day)
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->where(function ($query) use ($start_date) {
                    if (!empty($start_date)) {
                        $query->where('start_date', '>', now()->format('Y-m-d'));
                    } else {
                        $query->where('start_date', '<=', now()->format('Y-m-d'));
                    }
                })
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    public function getWorkingHoursByDayAndClinicIdAndStartingDay($day, $clinic_id, $start_date = null)
    {
        try {
            return $this->workingHour->where('day', $day)
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->whereDate('start_date', $start_date)
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }


    /**
     *  get working hours from
     *
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getStartWorkingHoursUsingDay($day, $clinic_id)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 0)
                ->orderBy('id', 'asc')
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get working hours to
     *
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getEndWorkingHoursUsingDay($day, $clinic_id)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 0)
                ->orderBy('id', 'desc')
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  add new working hour to clinic by day and time and add the created by
     *
     * @param $clinic_id
     * @param $days
     * @param $time
     * @param $startDate
     * @return mixed
     */
    public function createNewWorkingHours($clinic_id, $days, $time, $startDate)
    {
        try {
            foreach ($days as $day) {
                $this->workingHour->create([
                    'time' => date('H:i', strtotime($time)),
                    'clinic_id' => $clinic_id,
                    'day' => $day,
                    'start_date' => $startDate,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of id of day working hours in clinic
     *
     * @param $clinic_id
     * @param $day
     * @return array
     */
    public function getIdsOfWorkingHoursByClinicIdAndDay($clinic_id, $day)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 0)
                ->get()
                ->pluck('id')
                ->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return [];
        }
    }

    /**
     *  get working hours by id
     *
     * @param $id
     * @return mixed
     */
    public function getWorkingHoursById($id)
    {
        try {
            return $this->workingHour->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get working hours that is free in this clinic
     *
     * @param $clinic_id
     * @param $day
     * @param $dayIndex
     * @return mixed
     */
    public function getWorkingHoursInClinicThatIsNotReserved($clinic_id, $day, $dayIndex)
    {
        // get the day of reservation start date and expiry date region
        $min_max_of_workingHours = $this->getMinAndMaxOfWorkingHours($dayIndex, $clinic_id);
        $start = null;
        $end = null;
        if ($min_max_of_workingHours != null) {
            if (Carbon::parse($min_max_of_workingHours->min_date) === Carbon::parse($min_max_of_workingHours->max_date)) {
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
            return $this->workingHour->select('id', DB::raw('TIME_FORMAT(time, "%h:%i %p") as time'))
                ->where('clinic_id', $clinic_id)
                ->where('day', $dayIndex)
                ->where('is_break', 0)
                ->whereNotIn('id',
                    DB::table('reservations')
                        ->where('day', $day)
                        ->whereIn('status', [WebController::R_STATUS_APPROVED, WebController::R_STATUS_ATTENDED])
                        ->where('clinic_id', $clinic_id)
                        ->select('working_hour_id')
                        ->pluck('working_hour_id')
                )// check if reservation day is today => then get working hours after now
                ->where(function ($q) use ($day) {
                    if ($day === Carbon::now()->format('Y-m-d')) {
                        $q->where('working_hours.time', '>', Carbon::now()->toTimeString());
                    }
                })
                ->where(function ($query) use ($start, $end, $min_max_of_workingHours, $day) {
                    if (!is_null($min_max_of_workingHours)) {
                        $query->whereDate('working_hours.start_date', '>=', $start);
                        $query->whereDate('working_hours.start_date', '<=', $day);
                        if (!is_null($end)) {
                            $query->whereDate('working_hours.start_date', '<', $end);
                        }
                    }
                })
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $clinic_id
     * @param $day
     * @param $dayIndex
     * @return bool
     */
    public function checkIfDayOfReservationInRange($clinic_id, $day, $dayIndex)
    {
        $min_max_of_workingHours = $this->getMinAndMaxOfWorkingHours($dayIndex, $clinic_id);
        $start = null;
        $end = null;
        if ($min_max_of_workingHours != null) {
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
            return $this->workingHour->select('id', DB::raw('TIME_FORMAT(time, "%h:%i %p") as time'))
                ->where('clinic_id', $clinic_id)
                ->where('day', $dayIndex)
                ->where('is_break', 0)
                // check if reservation day is today => then get working hours after now
                ->where(function ($q) use ($day) {
                    if ($day == Carbon::now()->format('Y-m-d')) {
                        $q->where('working_hours.time', '>', Carbon::now()->toTimeString());
                    }
                })
                ->where(function ($query) use ($start, $end, $min_max_of_workingHours, $day) {
                    if (!is_null($min_max_of_workingHours)) {
                        $query->whereDate('working_hours.start_date', '>=', $start);
                        $query->whereDate('working_hours.start_date', '<=', $day);
                        if (!is_null($end)) {
                            $query->whereDate('working_hours.start_date', '<', $end);
                        }
                    }
                })
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get an array of working hours of clinic using day and clinic id
     *
     * @param $clinic_id
     * @param $day
     * @param $expiry_date
     * @return bool|array
     */
    public function getArrayOfWorkingHoursByClinicAndDay($clinic_id, $day, $expiry_date = null)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 0)
                ->where(function ($query) use ($expiry_date) {
                    if (!is_null($expiry_date)) {
                        $query->whereDate('expiry_date', '=', $expiry_date);
                    } else {
                        $query->whereNull('expiry_date');
                    }
                })
                ->orderBy('time', 'asc')
                ->pluck('time')
                ->toArray();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get array of trashed working hours using clinic_id
     * @param $clinic_id
     * @return mixed
     */
    public function getArrayOfTrashedWorkingHours($clinic_id)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->onlyTrashed()
                ->where('is_break', 0)
                ->get()
                ->pluck('id')
                ->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     * @param $clinics
     * @return mixed
     */
    public function getAllWorkingHours($clinics)
    {
        try {
            return $this->workingHour
                ->whereIn('clinic_id', $clinics)
                ->where('is_break', 0)
                ->where('deleted_at', null)
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get working hours using clinic id
     * @param $clinic_id
     * @param bool $is_old
     * @param null $day
     * @return mixed
     */
    public function getWorkingHoursByClinicId($clinic_id, $is_old = true, $day = null)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where(function ($query) use ($is_old) {
                    if ($is_old) {
                        // old times
                        $query->where('start_date', '<=', now()->format('Y-m-d'));
                    } else {
                        // new times
                        $query->where('start_date', '>', now()->format('Y-m-d'));
                    }
                })
                ->where('is_break', 0)
                ->where(function ($query) use ($day) {
                    // in case we want only one day
                    if (!is_null($day)) {
                        $query->where('day', $day);
                    }
                })
                ->select('day', DB::raw('min(time) as min_time'), DB::raw('max(time) as max_time'), DB::raw('min(start_date) as start_date'), DB::raw('min(expiry_date) as expiry_date'))
                ->groupBy('day')
                ->get();
        } catch (\Exception $e) {
            self::logErr('the error => ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @param $clinic_id
     * @param null $day
     * @return bool
     */
    public function getBreakWorkingHoursByClinicId($clinic_id, $day)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 1)
                ->groupBy('updated_at')
                ->select('updated_at', 'day', DB::raw('min(time) as min_time'), DB::raw('max(time) as max_time'))
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    public function getAllBreaksWorkingHoursByClinicId($clinic_id)
    {
        try {
            return $this->workingHour->where('clinic_id', $clinic_id)
                ->where('is_break', 1)
                ->groupBy('updated_at')
                ->select('updated_at', DB::raw('min(time) as min_time'), DB::raw('max(time) as max_time'))
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  check if the day that will be added is exists already or not
     *
     * @param $clinic_id
     * @param $days_indexes
     * @param $start_day
     * @return bool
     */
    public function checkIfWorkingHoursExistsOrNot($clinic_id, $days_indexes, $start_day)
    {
        try {
            $day_times = $this->workingHour
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->where(function ($query) use ($start_day, $days_indexes) {

                    // uses the day indexes to search in the datIndexes
                    if (isset($days_indexes)) {
                        if (is_array($days_indexes)) {
                            $query->whereIn('day', $days_indexes);
                        } else {
                            $query->where('day', $days_indexes);
                        }
                    }

                    if ($start_day == null) {
                        // in case of current dates
                        $query->whereDate('start_date', '<=', now()->format('Y-m-d'));
                    } else {
                        // in case of future dates
                        $query->whereDate('start_date', '>', now()->format('Y-m-d'));
                    }
                })
                ->count();
            if ($day_times > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function UpdateOldWorkingHoursWithExpiry_date($clinic_id, $day, $expiry_date)
    {
        // update all previous old working hours to expiry date
        try {
            DB::table('working_hours')
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->whereIn('day', $day)
                ->whereDate('start_date', '<=', $expiry_date)
                ->update(['expiry_date' => $expiry_date]);

        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        return true;
    }

    public function UpdateOldWorkingHoursWhenDeleteNew($clinic_id, $day, $start)
    {
        // update all previous old working hours to expiry date
        try {
            DB::table('working_hours')
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->where('day', $day)
                ->where('expiry_date', '!=', null)
                ->whereDate('expiry_date', '<=', $start)
                ->update(['expiry_date' => null]);

        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        return true;
    }

    public function getMinAndMaxOfWorkingHours($dayIndex, $clinic_id)
    {
        try {
            return $this->workingHour
                ->where('day', $dayIndex)
                ->where('is_break', 0)
                ->where('clinic_id', $clinic_id)
                ->select('day', DB::raw('min(start_date) as min_date'), DB::raw('max(start_date) as max_date'))
                ->groupBy('day')
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get expiry date of working hours
     *
     * @param $id
     * @return mixed
     */
    public function getExpiryDateOfWorkingHoursById($id)
    {
        try {
            return $this->workingHour->where('id', $id)->first()->expiry_date;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update workingHours to add breaks
     *
     * @param $clinic_id
     * @param $from
     * @param $to
     * @param $daysIndexes
     * @return bool
     */
    public function updateIntervalWorkingHoursToAddBreaks($clinic_id, $from, $to, $daysIndexes)
    {

        // to reject any day breaks that dont have workingHour
        $used_days = $this->getArrayOfDayIndexesByClinicId($clinic_id);
        foreach ($daysIndexes as $dayIndex) {
            if (!in_array($dayIndex, $used_days)) {
                return false;
            }
        }

        try {
            $wh = $this->workingHour
                ->whereIn('day', array_values($daysIndexes))
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 0)
                ->where(function ($query) use ($from, $to) {
                    $query->where('time', '>=', $from);
                    $query->where('time', '<', $to);
                })
                ->update(['is_break' => 1]);

            return $wh;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  insert the break data for Queue
     *
     * @param $clinic_id
     * @param $from
     * @param $to
     * @param $daysIndexes
     * @return bool
     */
    public function addWorkingHourForQueueBreaks($clinic_id, $from, $to, $daysIndexes)
    {
        $data = array();

        // get array of workingHours days indexes in that clinic
        $used_days = $this->getArrayOfDayIndexesByClinicId($clinic_id);
        foreach ($daysIndexes as $dayIndex) {
            if (in_array($dayIndex, $used_days)) {
                $data[] = array('time' => $from, 'clinic_id' => $clinic_id, 'day' => $dayIndex, 'start_date' => now()->format('Y-m-d'), 'is_break' => 1, 'created_at' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s'));
                $data[] = array('time' => $to, 'clinic_id' => $clinic_id, 'day' => $dayIndex, 'start_date' => now()->format('Y-m-d'), 'is_break' => 1, 'created_at' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s'));
            } else {
                return false;
            }
        }

        try {
            $this->workingHour->insert($data);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  delete breaks from workingHours interval
     *
     * @param $clinic_id
     * @param $day
     * @param $updated_at
     * @return bool
     */
    public function deleteIntervalBreaksFromClinic($clinic_id, $day, $updated_at)
    {
        try {
            return $this->workingHour
                ->where('day', $day)
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 1)
                ->where('updated_at', $updated_at)
                ->update(['is_break' => 0]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  delete breaks from workingHours queue
     *
     * @param $clinic_id
     * @param $day
     * @param $updated_at
     * @return bool
     */
    public function deleteQueueBreaksFromClinic($clinic_id, $day, $updated_at)
    {
        try {
            return $this->workingHour
                ->where('day', $day)
                ->where('clinic_id', $clinic_id)
                ->where('is_break', 1)
                ->where('updated_at', $updated_at)
                ->delete();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $clinic_id
     * @return mixed
     */
    public function getArrayOfDayIndexesByClinicId($clinic_id)
    {
        return $this->workingHour
            ->where('clinic_id', $clinic_id)
            ->where('is_break', 0)
            ->select('day', DB::raw('min(start_date) as min_date'), DB::raw('max(start_date) as max_date'))
            ->groupBy('day')
            ->pluck('day')
            ->toArray();
    }
}
