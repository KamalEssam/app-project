<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:150',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
            'message' => 'required',
        ];

    }
/*    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'email.required'  => 'Email is required',
            'mobile.required' => 'Mobile is required',
            'message.required'  => 'Message is required',

        ];
    }*/
}