<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:30 AM
 */

namespace App\Http\Interfaces\Validation;


use Illuminate\Http\Request;

interface MarketPlaceValidationInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function productDetailsValidation(Request $request);


    /**
     *  redeem product
     *
     * @param Request $request
     * @return mixed
     */
    public function redeemProductValidation(Request $request);
}
