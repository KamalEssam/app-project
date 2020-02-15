<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdRequest extends FormRequest
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
                'screen_shot' => 'required|image|mimes:jpeg,jpg,png|dimensions:width=285,height=523',
                'background' => 'required|image|mimes:jpeg,jpg,png|dimensions:width=1140,height=455',
                'en_title' => 'required',
                'ar_title' => 'required',
                'en_desc' => 'required',
                'ar_desc' => 'required',
                'type' => 'required',
                'time_from' => 'required',
                'time_to' => 'required',
                'date_from' => 'required',
                'date_to' => 'required|date|after:date_from',
                'slide' => 'required',
                'priority' => 'required',
            ];
        }

        return [
            'screen_shot' => 'nullable|image|mimes:jpeg,jpg,png|dimensions:width=285,height=523',
            'background' => 'nullable|image|mimes:jpeg,jpg,png|dimensions:width=1140,height=455',
            'en_title' => 'required',
            'ar_title' => 'required',
            'en_desc' => 'required',
            'ar_desc' => 'required',
            'type' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'date_from' => 'required',
            'date_to' => 'required|date|after:date_from',
            'slide' => 'required',
            'priority' => 'required',
        ];
    }
}
