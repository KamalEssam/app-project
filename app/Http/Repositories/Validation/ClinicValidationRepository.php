<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:31 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\ClinicValidationInterface;
use Illuminate\Http\Request;
use Validator;


class ClinicValidationRepository extends ValidationRepository implements ClinicValidationInterface
{
    /**
     * @param Request $request
     * @param array $appeded
     * @return mixed
     */
    public function getClinicIdValidation(Request $request, $appeded = [])
    {
        $validator = Validator::make($request->all(), array_merge([
                'clinic_id' => 'required|exists:clinics,id',
                'start_day' => 'sometimes',
            ], $appeded)
        );
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}
