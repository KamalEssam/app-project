<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;



interface ClinicInterface
{
    /**
     * get this clinic days
     * @param $clinic_id
     * @param $start_date
     * @return mixed
     */
    public function getClinicDays($clinic_id, $start_date);

    /**
     * @param $clinic_id
     * @return mixed
     */
    public function getClinicById($clinic_id);

    /**
     * get all clinics related to same account
     * @param $account_id
     * @return mixed
     */
    public function getClinicsRelatedToSameAccount($account_id);

    /**
     * get clinic pattern for auth user
     * @param $user
     * @return mixed
     */
    public function getClinicPattern($user);

}