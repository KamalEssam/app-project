<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface DoctorDetailInterface
{
    /**
     *  get the accounts count
     *
     * @return mixed
     */
    public function getDoctorDetailsByAccountId($account_id);

    /**
     *  create speciality id
     *
     * @param $account_id
     * @param $speciality_id
     * @return mixed
     */
    public function createDoctorDetail($account_id, $speciality_id);

    /**
     *  update min fees
     *
     * @param $except
     * @return mixed
     */
    public function updateMinFees($except);
}
