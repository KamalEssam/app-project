<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;

use App\Http\Controllers\ApiController;
use App\Http\Interfaces\Api\WorkingHourInterface;
use App\Models\Reservation;
use App\Models\WorkingHour;
use App\Http\Traits\DateTrait;
use Carbon\Carbon;
use DB;

class WorkingHourRepository implements WorkingHourInterface
{
    use DateTrait;

    /**
     * get day working hours
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getWorkingHoursInClinicThatIsNotReservedOrOver($day, $clinic_id)
    {

        $dayIndex = DateTrait::getDayIndex($day);

        $min_max_of_workingHours = (new \App\Http\Repositories\Web\WorkingHourRepository())->getMinAndMaxOfWorkingHours($dayIndex, $clinic_id);
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

        // get requested day index ans today index
        try {
            $dayIndex = self::getDayIndex($day);
            $today_index = self::getDayIndex(self::getDateByFormat(self::getToday(), 'Y-m-d'));
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        // get all times in this day in this clinic when it's not over or reserved
        try {
            $times = WorkingHour::where('clinic_id', $clinic_id)
                ->where('day', $dayIndex)
                ->where('working_hours.is_break', 0)// not in break
                ->whereNull('working_hours.deleted_at')
                ->whereNotIn('id', Reservation::join('clinics', 'reservations.clinic_id', 'clinics.id')
                    ->where('clinics.pattern', ApiController::PATTERN_INTERVAL)
                    ->where('reservations.day', $day)
                    ->whereIn('reservations.status', [ApiController::STATUS_APPROVED, ApiController::STATUS_ATTENDED])
                    ->where('reservations.clinic_id', $clinic_id)
                    ->select('reservations.working_hour_id')
                    ->pluck('reservations.working_hour_id'))
                ->where(function ($query) use ($start, $end, $min_max_of_workingHours) {
                    if (!is_null($min_max_of_workingHours)) {
                        $query->whereDate('working_hours.start_date', '>=', $start);
                        if (!is_null($end)) {
                            $query->whereDate('working_hours.start_date', '<', $end);
                        }
                    }
                })
                ->where(function ($query) use ($day, $clinic_id, $today_index) {
                    if (self::getDateByFormat($day, 'Y-m-d') == self::getDateByFormat(self::getToday(), 'Y-m-d')) {
                        $query->whereNotIn('id', WorkingHour::join('clinics', 'working_hours.clinic_id', 'clinics.id')
                            ->where('working_hours.clinic_id', $clinic_id)
                            ->where('clinics.pattern', ApiController::PATTERN_INTERVAL)
                            ->where('working_hours.day', $today_index)
                            ->where('working_hours.time', '<', self::getTimeByFormat(Carbon::now('Africa/Cairo'), 'H:i:s'))
                            ->pluck('working_hours.id'));
                    }
                })
                ->select('id', 'is_break', DB::raw('TIME_FORMAT(time, "%h:%i") as time'), DB::raw('TIME_FORMAT(time, "%p") as time_range'))
                ->get()->map(function ($item, $key) {

                    if (app()->getLocale() === 'ar') {
                        $item->time = self::enToAr($item->time, false);
                        $item->time_range = self::TranslateTimeRange($item->time_range);
                    }
                    return $item;
                });
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }
        return $times;
    }

    public function getArrayOfBreakTimesInQueue($clinic_id, $day)
    {
        try {
            return WorkingHour::where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 1)
                ->groupBy('updated_at')
                ->select('updated_at', DB::raw('min(TIME_FORMAT(time, "%h:%i:%p")) as time_from'), DB::raw('max(TIME_FORMAT(time, "%h:%i:%p")) as time_to'))
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    public function getWorkingHoursByClinicId($clinic_id, $day = null, $reservation_day = null)
    {
        try {
            return WorkingHour::where('clinic_id', $clinic_id)
                ->where(function ($query) use ($reservation_day, $day, $clinic_id) {

                    if ($reservation_day) {
                        $min_max_of_workingHours = (new \App\Http\Repositories\Web\WorkingHourRepository())->getMinAndMaxOfWorkingHours($day, $clinic_id);
                        $start = null;
                        $end = null;
                        if ($min_max_of_workingHours != null) {
                            if (Carbon::parse($min_max_of_workingHours->min_date) == Carbon::parse($min_max_of_workingHours->max_date)) {
                                $query->where('start_date', '>=', $min_max_of_workingHours->min_date);
                            } else {
                                if (Carbon::parse($reservation_day) >= Carbon::parse($min_max_of_workingHours->max_date)) {
                                    $query->where('start_date', '>=', $min_max_of_workingHours->max_date);
                                } else {
                                    $query->where('start_date', '>=', $min_max_of_workingHours->min_date);
                                }
                            }
                        }
                    }
                })
                ->where('is_break', 0)
                ->where(function ($query) use ($day) {
                    // in case we want only one day
                    if ($day != null) {
                        $query->where('day', $day);
                    }
                })
                ->select('day', DB::raw('min(time) as min_time'), DB::raw('max(time) as max_time'), DB::raw('min(start_date) as start_date'), DB::raw('min(expiry_date) as expiry_date'))
                ->groupBy('day')
                ->first();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
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
            return WorkingHour::where('clinic_id', $clinic_id)
                ->where('day', $day)
                ->where('is_break', 1)
                ->groupBy('updated_at')
                ->select('updated_at', 'day', DB::raw('min(time) as start'), DB::raw('max(time) as end'))
                ->get();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }
}
