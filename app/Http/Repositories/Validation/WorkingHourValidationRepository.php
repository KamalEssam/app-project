<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:31 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\WorkingHourValidationInterface;
use Illuminate\Http\Request;
use Validator;


class WorkingHourValidationRepository extends ValidationRepository implements WorkingHourValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getDayWorkingHoursValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinic_id' => 'required|exists:clinics,id',
            'day' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}