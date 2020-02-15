<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface WorkingHourInterface
{
    /**
     *  get working hours using day number and clinic id
     *
     * @param $day
     * @param $clinic_id
     * @param null $start_date
     * @return mixed
     */
    public function getWorkingHoursByDayAndClinicId($day, $clinic_id,$start_date = null);

    /**
     * get working hours using clinic id
     * @param $clinic_id
     * @param bool $is_old
     * @param null $day
     * @return mixed
     */
    public function getWorkingHoursByClinicId($clinic_id, $is_old = true,$day=null);


    /**
     *  get working hours from
     *
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getStartWorkingHoursUsingDay($day, $clinic_id);

    /**
     *  get working hours to
     *
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function getEndWorkingHoursUsingDay($day, $clinic_id);

    /**
     *  add new working hour to clinic by day and time and add the created by
     *
     * @param $clinic_id
     * @param $day
     * @param $time
     * @param $startDate
     * @return mixed
     */
    public function createNewWorkingHours($clinic_id, $day, $time, $startDate);

    /**
     * @param $clinic_id
     * @param $day
     * @return mixed
     */
    public function getIdsOfWorkingHoursByClinicIdAndDay($clinic_id, $day);

    /**
     *  get working hours by id
     *
     * @param $id
     * @return mixed
     */
    public function getWorkingHoursById($id);

    /**
     * get working hours that is free in this clinic
     *
     * @param $clinic_id
     * @param $day
     * @param $dayIndex
     * @return mixed
     */
    public function getWorkingHoursInClinicThatIsNotReserved($clinic_id, $day, $dayIndex);


    /**
     *  get an array of working hours of clinic using day and clinic id
     *
     * @param $clinic_id
     * @param $day
     * @param null $expiry_date
     * @return mixed
     */
    public function getArrayOfWorkingHoursByClinicAndDay($clinic_id, $day,$expiry_date = null);

    /**
     *  get array of ids of trashed working hours
     *
     * @return mixed
     */
    public function getArrayOfTrashedWorkingHours($clinic_id);

    /**
     * @param $clinics
     * @return mixed
     */
    public function getAllWorkingHours($clinics);
}