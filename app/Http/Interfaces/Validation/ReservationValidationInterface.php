<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:30 AM
 */

namespace App\Http\Interfaces\Validation;

use Illuminate\Http\Request;

interface ReservationValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function addReservationValidation(Request $request);

    /**
     * check reservation id to reschedule reservation
     * @param Request $request
     * @return mixed
     */
    public function reservationIdValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function setStatusValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClinicIdValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function nextQueueValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function setStandBy(Request $request);

}