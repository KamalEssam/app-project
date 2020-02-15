<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
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
                'email' => 'required|email|unique:users,email',
                'name' => 'required',
                'mobile' => 'required|numeric|phone_number|digits:11|unique:users,mobile',
                'address' => 'required',
                'birthday' => 'required|date',
                'gender' => 'required',
            ];
        } elseif ($this->method() == 'PATCH') {
            return [
                'email' => 'required|email|unique:users,email,'. $this->route()->id,
                'name' => 'required',
                'mobile' => 'required|numeric|phone_number|digits:11|unique:users,mobile,'. $this->route()->id,
                'address' => 'required',
                'birthday' => 'required|date',
                'gender' => 'required',
            ];
        }
    }
}
