<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
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
        if (auth()->user()->role_id == 2) {
            return [
                'day' => 'required|date',
                'ar_reason' => 'required|string',
                'en_reason' => 'required|string',
            ];
        } elseif (auth()->user()->role_id == 1 && auth()->user()->account->type == 1) {
            return [
                'day' => 'required|date',
                'ar_reason' => 'required|string',
                'en_reason' => 'required|string',
                'clinics' => 'required|array'
            ];
        }
    }
}
