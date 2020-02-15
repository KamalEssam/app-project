<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
                'email' => 'required|email|unique:users',
                'name' => 'required',
                'mobile' => 'required|numeric|phone_number|digits:11|unique:users,mobile',
                'plan_id' => 'required',
                'country_id' => 'required',
                'city_id' => 'required|numeric',
            ];
        } elseif ($this->method() == 'PATCH') {
            return [
                'plan_id' => 'required',
                'days' => 'required',
            ];
        }
    }
}
