<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubSpecialityRequest extends FormRequest
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
    public function rules(): array
    {
        if ($this->method() == 'POST') {
            return [
                'en_name' => 'required|array',
                'ar_name' => 'required|array',
                'speciality_id' => 'required|exists:specialities,id'
            ];
        } else {
            return [
                'en_name' => 'required',
                'ar_name' => 'required',
                'speciality_id' => 'required|exists:specialities,id'
            ];
        }
    }
}
