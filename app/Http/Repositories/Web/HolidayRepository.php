<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\HolidayInterface;
use App\Models\Holiday;
use Illuminate\Database\Eloquent\Collection;

class HolidayRepository extends ParentRepository implements HolidayInterface
{
    public $holiday;

    public function __construct()
    {
        $this->holiday = new Holiday();
    }

    /**
     *  create new holiday
     *
     * @param $request
     * @return mixed
     */
    public function createHoliday($request)
    {
        try {
            return $this->holiday->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get list of halides by clinic id
     *
     * @param $clinic_id
     * @return mixed
     */
    public function getHalidesByClinicId($clinic_id)
    {
        try {
            return $this->holiday
                ->where(function ($query) use ($clinic_id) {
                    if (is_array($clinic_id)) {
                        $query->whereIn('clinic_id', $clinic_id);
                    } else {
                        $query->where('clinic_id', $clinic_id);
                    }
                })->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     * get holiday using clinic and day
     *
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getHolidayByDayAndClinic($day, $clinic_id)
    {
        try {
            return $this->holiday->where('day', $day)
                ->where('clinic_id', $clinic_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get array of clinic days
     *
     * @param $clinic_id
     * @return bool|mixed
     */
    public function getArrayOfDaysOfHolidayUsingClinic($clinic_id)
    {
        try {
            return $this->holiday->where('clinic_id', $clinic_id)->pluck('day')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}