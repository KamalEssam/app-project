<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;



interface WorkingHourInterface
{
    /**
     * get working hours that is free in this clinic
     * @param $clinic_id
     * @param $day
     * @return mixed
     */
    public function getWorkingHoursInClinicThatIsNotReservedOrOver($day, $clinic_id);

}