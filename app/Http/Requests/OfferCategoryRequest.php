<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferCategoryRequest extends FormRequest
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
            $appeded['image'] = 'required|image|mimes:jpeg,jpg,png|dimensions:width=65,height=65';
        } else {
            $appeded['image'] = 'nullable|image|mimes:jpeg,jpg,png|dimensions:width=65,height=65';
        }

        if ($this->method() == 'POST') {
            return array_merge($appeded, [
                'en_name' => 'required|unique:offers_categories,en_name',
                'ar_name' => 'required|unique:offers_categories,ar_name',
                'speciality_id' => 'required|numeric|exists:specialities,id'
            ]);
        }

        return array_merge($appeded, [
            'en_name' => 'required|unique:offers_categories,en_name,' . $this->route()->id,
            'ar_name' => 'required|unique:offers_categories,ar_name,' . $this->route()->id,
            'speciality_id' => 'required|numeric|exists:specialities,id'
        ]);
    }
}
