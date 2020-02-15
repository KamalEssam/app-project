<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;

use App\Http\Controllers\Api\ReservationController;

interface ReservationInterface

{
    /**
     * get status name (0=>pending, 1=>approved, 2=>canceled, 3=>attended, 4=>missed)
     * @param $status
     * @return mixed
     */
    public function getStatusName($status);

    /**
     * create new reservation
     * @param $request
     * @return mixed
     */
    public function createReservation($request);

    /**
     * edit reservation
     * @param $request
     * @param $user
     * @return mixed
     */
    public function editReservation($request, $user);

    /**
     * update reservation status and created by
     * @param $reservation
     * @param $user_id
     * @param null $largest_queue
     * @return mixed
     */
    public function updateReservationData($reservation, $user_id, $largest_queue = null);

    /**
     * @param $clinic_id
     * @return mixed
     */
    public function getQueueToday($clinic_id);

    /**
     * get working hour by id
     * @param $working_hour_id
     * @return mixed
     */
    public function getWorkingHourById($working_hour_id);

    /**
     * get reservation by id
     * @param $reservation_id
     * @return mixed
     */
    public function getReservationById($reservation_id);

    /**
     * check if working hour belong to this day and this clinic
     * @param $working_hour_id
     * @param $clinic
     * @param $day
     * @return mixed
     */
    public function checkWorkingHourBelongToClinic($working_hour_id, $clinic, $day);

    /**
     * check if working hour reserved or not
     * @param $working_hour_id
     * @param $day
     * @return mixed
     */
    public function checkIfWorkingHourReserved($working_hour_id, $day);

    /**
     * get clinic by id
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicById($clinic_id);

    /**
     * check if patient want to reserve in time passed
     * @param $day
     * @param $time
     * @return mixed
     */
    public function checkTimePassed($day, $time);


    /**
     * get largest queue to increase it in new reservation
     * @param $clinic_id
     * @param $day
     * @return mixed
     */
    public function getLargestQueue($clinic_id, $day);

    /**
     * @param null $clinic_id
     * @param $user_id
     * @param $check
     * @return mixed
     */
    public function checkIfReservationUpcoming($user_id, $check, $clinic_id = null);


    /**
     * @param $status
     * @param $day
     * @param $time
     * @param $estimated_time
     * @return mixed
     */
    public function getUpcomingReservationFormatted($status, $day, $time, $estimated_time);

    /**
     * get patients count when clinic work with queue
     * @param $upcoming_reservation
     * @return bool
     */
    public function getPatientsCount($upcoming_reservation);


    /**
     * get all approved reservation today
     * @param $clinic_id
     * @return mixed
     */
    public function getApprovedReservationsCountToday($clinic_id);

    /**
     * get min working hours to get start clinic
     * @param $clinic_id
     * @param $index
     * @param $day
     * @return mixed
     */
    public function getClinicStartTime($clinic_id, $index, $day);

    /**
     * get first reservation attended
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getFirstReservationAttended($upcoming_reservation);

    /**
     * get all attended reservation before me
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getAttendedPatientsBeforeThisPatient($upcoming_reservation);

    /**
     * get all attended reservation before me if clinic work queue
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getAttendedReservationAndFirstReservation($upcoming_reservation);

    /**
     * get upcoming estimated time when clinic start
     * @param $patients_attended
     * @param $first_reservation_check_in
     * @param $patients_approved_count
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getUpcomingReservationEstimatedTimeAfterClinicStart($patients_attended, $first_reservation_check_in, $patients_approved_count, $upcoming_reservation);

    /**
     * get upcoming estimated time before clinic start
     * @param $patients_approved_count
     * @param $clinic_start_time
     * @param $upcoming_reservation
     * @return mixed
     */
    public function getUpcomingReservationTimeAndEstimatedTimeBeforeClinicStart($patients_approved_count, $clinic_start_time, $upcoming_reservation);


    /**
     * to hide some data
     * @param $upcoming_reservation
     * @param $formatted
     * @return mixed
     */
    public function setUpcomingReservationData($upcoming_reservation, $formatted);

    /**
     * get all clinics with same created by with this clinic
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicsWithSameCreatedBy($clinic_id);

    /**
     * get all clinics related to account
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicsWithSameAccount($clinic_id);

    /**
     * get user who create reservation
     * @param $reservation_created_by
     * @return mixed
     */
    public function getReservationCreatedByUser($reservation_created_by);

    /**
     * get reservation fees ( subtotal_fees, VAT , TotalFees)
     * @param $services
     * @param $clinic_id
     * @param $type (check_up , follow_up)
     * @param $offer_id
     * @return mixed
     */
    public function getReservationFees($services,$clinic_id, $type, $offer_id);

    /**
     * get upcoming reservation for this patient with this doctor
     * @param $user_id
     * @param $account_id
     * @return mixed
     */
    public function getUpcomingReservationWithThisDoctor($user_id, $account_id);

    /**
     * cancel reservation when patient remove doctor from list
     * @param $reservation_id
     * @return mixed
     */
    public function cancelReservation($reservation_id);

    /**
     * get upcoming reservation details
     * @param $reservation_id
     * @param $user
     * @param $reservationController
     * @return mixed
     */
    public function getUpcomingReservationDetails($reservation_id, $user, ReservationController $reservationController);

    /**
     * get clinic queue if start
     * @param $reservation
     * @return mixed
     */
    public function getClinicQueueToday($reservation);

    /**
     * get the reservation that with doctor
     * @param $reservation
     * @return mixed
     */
    public function getReservationWithDoctor($reservation);

    /**
     * check if this day is holiday
     * @param $day
     * @param $reservation
     * @return mixed
     */
    public function checkIfHoliday($day, $reservation);

    /**
     * check if patient have reservation with this doctor today and he can not add another if he has reservation approved or missed
     * @param $clinic_id
     * @param $user_id
     * @param $day
     * @return mixed
     */
    public function checkIfPatientReserveWithThisDoctorTwiceAtDayWhenAdd($clinic_id, $user_id, $day);

    /**
     * check if patient have reservation with this doctor today and he can not reschedule it if he has reservation attended or canceled
     * @param $clinic_id
     * @param $user_id
     * @param $day
     * @return mixed
     */
    public function checkIfPatientReserveWithThisDoctorTwiceAtDayWhenReschedule($clinic_id, $user_id, $day);


    /**
     * check if patient have reservation past
     * @param $user_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function checkIfReservationPast($user_id, $offset, $limit);
}
