<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientPlanRequest extends FormRequest
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
            'en_name' => 'required',
            'ar_name' => 'required',
            'price' => 'required|numeric',
            'months' => 'required|numeric',
            'en_desc' => 'required',
            'ar_desc' => 'required',
            'points' => 'required|numeric|min:0',
        ];
    }
}
