<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 9:54 AM
 */

namespace App\Http\Interfaces\Validation;


Interface ValidationInterface
{
    /**
     * @return mixed
     */
    public function getErrors();

    /**
     * @return mixed
     */
    public function getFirstError();
}