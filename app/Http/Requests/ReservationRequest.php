<?php

namespace App\Http\Requests;

use App\Rules\Match;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
                'mobile' => 'required|numeric|phone_number|digits:11|exists:users,mobile',
                'user_name' => 'required',
                'clinic_id' => 'required|numeric|exists:clinics,id',
                'day' => 'required',
                'pattern' => 'required|numeric|min:0|max:1',
                'working_hour_id' => 'required_if:pattern,0,|numeric|exists:working_hours,id',
                'type' => 'required',
            ];
        } elseif ($this->method() == 'PATCH') {
            return [
                'clinic_id' => 'required|numeric|exists:clinics,id',
                'day' => 'required',
                'pattern' => 'required|numeric|min:0|max:1',
                'working_hour_id' => 'required_if:pattern,0,|numeric|exists:working_hours,id',
                'type' => 'required',
            ];
        }
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'working_hour_id.required_if' => 'there is no available appointments in this day',
        ];
    }
}