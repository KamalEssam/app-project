<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialityRequest extends FormRequest
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
                'en_speciality' => 'required|unique:specialities,en_speciality',
                'ar_speciality' => 'required|unique:specialities,ar_speciality'
            ];
        }
        return [
            'en_speciality' => 'required|unique:specialities,en_speciality,' . $this->route()->id,
            'ar_speciality' => 'required|unique:specialities,ar_speciality,' . $this->route()->id
        ];
    }
}
