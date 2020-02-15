<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface HolidayInterface
{
    /**
     *  create new holiday
     *
     * @param $request
     * @return mixed
     */
    public function createHoliday($request);

    /**
     * get list of halides by clinic id
     *
     * @param $clinic_id
     * @return mixed
     */
    public function getHalidesByClinicId($clinic_id);

    /**
     * get holiday using clinic and day
     *
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getHolidayByDayAndClinic($day, $clinic_id);

    /**
     * @param $clinic_id
     * @return mixed
     */
    public function getArrayOfDaysOfHolidayUsingClinic($clinic_id);
}