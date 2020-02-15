<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceCompaniesRequest extends FormRequest
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
                'en_name' => 'required|unique:insurance_companies,en_name',
                'ar_name' => 'required|unique:insurance_companies,ar_name'
            ];
        } else {
            return [
                'en_name' => 'required|unique:insurance_companies,en_name,' . $this->route()->id,
                'ar_name' => 'required|unique:insurance_companies,ar_name,' . $this->route()->id
            ];
        }
    }
}
