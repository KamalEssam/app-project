<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/1/18
 * Time: 4:39 PM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\PatientValidationInterface;
use Illuminate\Http\Request;
use Validator;

class PatientValidationRepository extends ValidationRepository implements PatientValidationInterface
{
    /**
     *  set add patient validation
     *
     * @param Request $request
     * @return bool|mixed
     */
    public function addPatientValidation(Request $request)
    {
        $messages = [
            'mobile.unique' => 'The mobile number has already been taken',
            'mobile.phone_number' => 'The mobile number not correct',
        ];
        // validate fields
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|phone_number|digits:11|unique:users,mobile',
        ], $messages);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }


    /**
     *  user premium request
     *
     * @param Request $request
     * @return bool
     */
    public function premiumRequest(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|numeric|exists:patients_plans,id',
            'type' => 'required|min:0|max:1'

        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}
