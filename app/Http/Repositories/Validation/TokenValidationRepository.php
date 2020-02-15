<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:31 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\TokenValidationInterface;
use Illuminate\Http\Request;
use Validator;


class TokenValidationRepository extends ValidationRepository implements TokenValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function setTokenValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial' => 'required',
            'token' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
}