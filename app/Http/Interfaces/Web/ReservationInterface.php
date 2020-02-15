<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface ReservationInterface
{
    /**
     *  get all clinic reservations in the given day
     *
     * @param $clinic_id
     * @param $day
     * @return mixed
     */
    public function getFirstReservationByClinicAndDayOrdered($clinic_id, $day);

    /**
     * create new reservations
     *
     * @param $request
     * @return mixed
     */
    public function createNewReservation($request);

    /**
     *  get reservation by id
     *
     * @param $id
     * @param array $status not required ( in case if ew want to add status)
     * @return mixed
     */
    public function getReservationById($id, $status = []);

    /**
     *  check if user has reservation on this day and clinic
     *
     * @param $user_id
     * @param $day
     * @param $clinic_id
     * @return mixed
     */
    public function checkIfUserHasReservation($user_id, $day, $clinic_id);

    /**
     *  update reservations
     *
     * @param $reservation
     * @param $request
     * @return mixed
     */
    public function updateReservation($reservation, $request);

    /**
     *  set cash reservation to Paid
     *
     * @param $reservation
     * @param $auth_id
     * @return mixed
     */
    public function setReservationToPaid($reservation, $auth_id);

    /**
     *  get one reservation by status and clinic_id and queue
     *
     * @param $status
     * @param string $clinic_id
     * @param string $queue
     * @param string $order
     * @return mixed
     */
    public function getReservationByStatusAndClinic($status, $clinic_id = '', $queue = '', $order = '');

    /**
     *  get one reservation by status and clinic_id and queue
     *
     * @param $status
     * @param string $clinic_id
     * @param string $queue
     * @param string $order
     * @return mixed
     */
    public function getAllReservationsByStatusAndClinic($status, $clinic_id = '', $queue = '', $order = '');

    /**
     *  change the reservation status after visit to attended or set it to missed
     *
     * @param $reservation
     * @param $status
     * @param $user_id
     * @return mixed
     */
    public function ChangeReservationStatusAfterVisit($reservation, $status, $user_id);

    /**
     *  add reservation check-in
     *
     * @param $reservation
     * @param $user_id
     * @param int $status status == 0 means check-in, status == 1 means check-out
     * @return mixed
     */
    public function addReservationCheckInAndOut($reservation, $user_id, $status = 0);

    /**
     * get collection of user reservations
     *
     * @param $user_id
     * @param array $status
     * @return mixed
     */
    public function getUSerReservationsByStatus($user_id, $status = []);

    /**
     * @param $status
     * @param string $clinic_id
     * @param string $queue
     * @return mixed
     */
    public function getNextReservationInQueue($status, $clinic_id = '', $queue = '');

    /**
     *  get count of all the reservations that is attended or approved
     *
     * @param string $clinic_id
     * @param string $day
     * @return mixed
     */
    public function getCountOfAllReservationsWhichIsApprovedAndAttended($clinic_id = '', $day = '');

    /**
     *  change the status of the reservation
     *
     * @param $reservation
     * @param $status
     * @return mixed
     */
    public function changeReservationStatus($reservation,$status);

    /**
     *  get the trashed working hours
     *
     * @param $trashedWorkingHours
     * @param $day
     * @param null $start_date
     * @param null $end_date
     * @return mixed
     */
    public function getReservationUsingTrashedWorkingHours($trashedWorkingHours,$pattern,$day,$start_date=null,$end_date=null);

    /**
     * get the number of reservation after today or certain date in specific clinic
     *
     * @param $clinic_id
     * @param $dayName
     * @param $start_date
     * @param $expiry_date
     * @return mixed
     */
    public function getReservationsCountInClinicAndDay($clinic_id, $dayName, $start_date,$expiry_date);
}