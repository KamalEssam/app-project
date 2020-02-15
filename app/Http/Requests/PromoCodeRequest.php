<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
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

        if ($this->discount_type == 1) {
            $appended['discount'] = 'required|numeric|lte:100';
        } else {
            $appended['discount'] = 'required|numeric';
        }

        if ($this->method() == 'POST') {
            return array_merge([
                'code' => 'required|unique:premium_promo_codes,code',
                'influencer_id' => 'required|exists:influencers,id',
                'expiry_date' => 'required|date',
                'discount_type' => 'required|numeric',
            ], $appended);
        } else {
            return array_merge([
                'code' => 'required|unique:premium_promo_codes,code,' . $this->route()->id,
                'influencer_id' => 'required|exists:influencers,id',
                'expiry_date' => 'required|date',
                'discount_type' => 'required|numeric',
            ], $appended);
        }
    }
}
