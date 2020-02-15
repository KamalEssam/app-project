<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
        $appeded = array();
        if ($this->method() == 'POST') {
            $appeded['image'] = 'required|image|mimes:jpeg,jpg,png|dimensions:min_width=500,min_height=250|dimensions:ratio=2/1';
        } else {
            $appeded['image'] = 'nullable|image|mimes:jpeg,jpg,png|dimensions:min_width=500,min_height=250|dimensions:ratio=2/1';
        }

        return array_merge($appeded, [
            'en_name' => 'required',
            'ar_name' => 'required',
            'en_desc' => 'required',
            'ar_desc' => 'required',
            'expiry_date' => 'required|date',
            'category_id' => 'required|numeric|exists:offers_categories,id',
            'doctor_id' => 'required|numeric|exists:users,id',
            'services' => 'required|array',
            'old_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|lt:old_price',
        ]);
    }
}
