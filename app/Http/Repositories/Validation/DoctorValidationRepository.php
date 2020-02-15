<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:31 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\DoctorValidationInterface;
use App\Rules\IsDoctor;
use Illuminate\Http\Request;
use Validator;


class DoctorValidationRepository extends ValidationRepository implements DoctorValidationInterface
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function getDoctorIdValidation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required', new IsDoctor($request->doctor_id)],
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addAndRemoveToFavouriteListValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function RecommendDoctorValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,unique_id',
            'user_id' => 'required|exists:users,unique_id',
            'receiver_serial' => 'required|string'
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClinicIdValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinic_id' => 'required|exists:clinics,id',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}