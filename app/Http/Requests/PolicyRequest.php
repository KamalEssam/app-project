<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PolicyRequest extends FormRequest
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
                'en_name' => 'required|unique:policies,en_name',
                'ar_name' => 'required|unique:policies,ar_name',
                'en_condition' => 'required',
                'ar_condition' => 'required',
            ];
        }

        return [
            'en_name' => 'required|unique:policies,en_name,' . $this->route()->id,
            'ar_name' => 'required|unique:policies,ar_name,' . $this->route()->id,
            'en_condition' => 'required|unique:policies,en_condition,' . $this->route()->id,
            'ar_condition' => 'required|unique:policies,ar_condition,' . $this->route()->id,
        ];
    }
}
