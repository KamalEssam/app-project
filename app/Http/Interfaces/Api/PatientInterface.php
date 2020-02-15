<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;


interface PatientInterface
{

    /**
     * get all clinics related to this doctor
     * @param $doctor_id
     * @return mixed
     */
    public function getClinicsRelatedToDoctor($doctor_id);

    /**
     * get all patients related to this doctor
     * @param $doctor
     * @param $request
     * @return mixed
     */
    public function getPatientsRelatedToThisDoctor($doctor, $request);
}