<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface VisitInterface
{

    /**
     * get list of all countries
     *
     * @param $reservation_id
     * @return mixed
     */
    public function getVisitByReservationId($reservation_id);


    /**
     *  get visits using patient name or date or both
     *
     * @param $auth_user
     * @param string $date
     * @param string $name
     * @return mixed
     */
    public function filterVisitByDateAndPatientName($auth_user, $date = '', $name = '');

    /**
     *  get all visits from Doctor
     *
     * @param $account
     * @return mixed
     */
    public function getAllVisitsForDoctorAccount($account);

    /**
     *  create new visit
     *
     * @param $request
     * @return mixed
     */
    public function createVisit($request);
}