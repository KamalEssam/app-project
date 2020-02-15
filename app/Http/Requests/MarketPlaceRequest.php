<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketPlaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'en_title' => 'required',
            'ar_title' => 'required',
            'en_desc' => 'required',
            'ar_desc' => 'required',
//            'points' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0.1',
            'max_redeems' => 'required|numeric|min:1',
            'redeem_expiry_days' => 'required|numeric|min:1',
            'brand_id' => 'required:exists:users,id',
            'market_place_category_id' => 'required:exists:market_place_category,id',
        ];
    }
}
