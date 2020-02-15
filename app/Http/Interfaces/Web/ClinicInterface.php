<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface ClinicInterface
{

    /**
     *  get clinic belongs to doctor and ordered by (created_at)
     *
     * @param $user_id
     * @return mixed
     */
    public function getDoctorClinicsOrdered($user_id);

    /**
     *  create new clinic (usually used by doctor)
     *
     * @param $request
     * @return mixed
     */
    public function createClinic($request);

    /**
     *  get clinic by id
     *
     * @param $id
     * @return mixed
     */
    public static function getClinicById($id);

    /**
     *  update the clinic data in case of Doctor And Assistant
     *
     * @param $clinic
     * @param $status
     * @param $request
     * @param $auth_id
     * @return mixed
     */
    public function updateClinicInDoctorAndAssistant($clinic, $status, $request, $auth_id);

    /**
     * @param $auth_id
     * @param $except
     * @return mixed
     */
    public function getCurrentMinFees($auth_id, $except);

}