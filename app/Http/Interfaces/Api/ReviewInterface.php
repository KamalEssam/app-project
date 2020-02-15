<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;


interface ReviewInterface
{
    /**
     * get doctor reviews by account id
     *
     * @param $account_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getDoctorReviews($account_id, $offset, $limit);


    /**
     *  add review to reservation
     *
     * @param $doctor_account_id
     * @param $user_id
     * @param $rate
     * @param $content
     * @param $reservation_id
     * @return mixed
     */
    public function addReview($doctor_account_id, $user_id, $rate, $content, $reservation_id);

    /**
     *  check if reservation has review or not
     *
     * @param $reservation_id
     * @return mixed
     */
    public function checkReservationReview($reservation_id);
}
