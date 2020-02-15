<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:31 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\ClinicValidationInterface;
use App\Http\Interfaces\Validation\SpecialityValidationInterface;
use Illuminate\Http\Request;
use Validator;


class SpecialityValidationRepository extends ValidationRepository implements SpecialityValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getSpecialityIdValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:specialities,slug',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}