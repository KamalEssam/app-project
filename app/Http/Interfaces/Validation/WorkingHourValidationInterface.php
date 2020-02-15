<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:30 AM
 */

namespace App\Http\Interfaces\Validation;


use Illuminate\Http\Request;

interface WorkingHourValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getDayWorkingHoursValidation(Request $request);

}