<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/1/18
 * Time: 4:39 PM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\ProfileValidationInterface;
use Illuminate\Http\Request;
use Validator;

class ProfileValidationRepository extends ValidationRepository implements ProfileValidationInterface
{

    /**
     * validation on image
     * @param Request $request
     * @return bool
     */
    public function setImageValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}