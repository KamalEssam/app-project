<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkingHourRequest extends FormRequest
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
        if (is_null($this['start_immediately'])) {
            return [
                'from' => 'required',
                'to' => 'required',
                'clinic_id' => 'required|exists:clinics,id',
                'day' => 'required|min:0|max:7',
                'start_date' => 'required'
            ];
        } else {
            return [
                'from' => 'required',
                'to' => 'required',
                'clinic_id' => 'required|exists:clinics,id',
                'day' => 'required|min:0|max:7',
            ];
        }
    }
}
