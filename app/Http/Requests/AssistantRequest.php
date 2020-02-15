<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistantRequest extends FormRequest
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
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|numeric|phone_number|digits:11|unique:users,mobile',
                'clinic_id' => 'required|exists:clinics,id'
            ];
        } else {
            return [
                'name' => 'required',
//                'email' => 'required|email|unique:users,id,' . $this->route()->id,
//                'mobile' => 'required|numeric|phone_number|digits:11|unique:users,mobile,' . $this->route()->id,
                'clinic_id' => 'required|exists:clinics,id',
            ];
        }
    }
}