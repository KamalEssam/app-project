<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/1/18
 * Time: 4:39 PM
 */

namespace App\Http\Repositories\Validation;


use App\Http\Interfaces\Validation\MarketPlaceValidationInterface;
use Illuminate\Http\Request;
use Validator;

class MarketPlaceValidationRepository extends ValidationRepository implements MarketPlaceValidationInterface
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function productDetailsValidation(Request $request)
    {
        {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:market_places,id',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }
            return true;
        }
    }

    /**
     *  redeem product
     *
     * @param Request $request
     * @return mixed
     */
    public function redeemProductValidation(Request $request)
    {
        {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:market_places,id',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }
            return true;
        }
    }
}
