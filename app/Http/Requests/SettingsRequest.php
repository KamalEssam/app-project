<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
            'email' => 'required|email',
            'mobile' => 'required|numeric|phone_number|digits:11',
            'website' => 'required',

            'facebook' => 'required',
            'twitter' => 'required',
            'youtube' => 'required',
            'googlepluse' => 'required',
            'instagram' => 'required',

            'en_about_us' => 'required',
            'ar_about_us' => 'required',
        ];
    }
}