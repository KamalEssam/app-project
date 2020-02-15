<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:30 AM
 */

namespace App\Http\Interfaces\Validation;


use Illuminate\Http\Request;

interface DoctorValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getDoctorIdValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function addAndRemoveToFavouriteListValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClinicIdValidation(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function RecommendDoctorValidation(Request $request);
}