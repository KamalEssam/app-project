<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorServiceRequest extends FormRequest
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

        if (auth()->user()->is_premium == 1) {
            $appended['premium_price'] = 'required';
        } else {
            $appended = [];
        }

        if ($this->method() == 'POST') {
            return array_merge($appended, [
                'service_id' => 'required|array|exists:services,id',
                'price' => 'required'
            ]);
        }

        return array_merge($appended, [
            'price' => 'required'
        ]);
    }
}
