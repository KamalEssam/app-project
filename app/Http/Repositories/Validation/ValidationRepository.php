<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 9:53 AM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\ValidationInterface;

class ValidationRepository implements ValidationInterface
{
    protected $errors;

    /**
     *  get all errors
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * get the first error
     *
     * @return mixed
     */
    public function getFirstError()
    {
        return $this->errors->first();
    }
}