<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
        if ($this->method() == 'POST') {
            return [
                'en_name' => 'required|unique:countries,en_name',
                'ar_name' => 'required|unique:countries,ar_name',
                'dialing_code' => 'required|regex:/^[0-9]{2,3}$/'
            ];
        } else {
            return [
                'en_name' => 'required|unique:countries,en_name,' . $this->route()->id,
                'ar_name' => 'required|unique:countries,ar_name,' . $this->route()->id,
                'dialing_code' => 'required|regex:/^[0-9]{2,3}$/'
            ];
        }
    }
}
