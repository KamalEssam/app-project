<?php

namespace App\Http\Requests;

use App\Rules\ArabicClinic;
use App\Rules\EnglishClinic;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;


class ClinicRequest extends FormRequest
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
        if (!is_null($this->pattern)) {
            // in case of single clinic
            if (auth()->user()->account->type == 0) {

                if (auth()->user()->is_premium == 1) {
                    $premium_case = [
                        'premium_fees' => 'required|numeric|min:1|lt:fees',
                        'premium_follow_up_fees' => 'required|numeric|min:1|lt:premium_fees',
                    ];
                } else {
                    $premium_case = [];
                }
                return array_merge([
                    'en_address' => 'required',
                    'ar_address' => 'required',
                    'lat' => 'required',
                    'lng' => 'required',
                    'pattern' => 'required|min:0|max:1',
                    'res_limit' => 'required|numeric|min:0',
                    'fees' => 'required|numeric|min:1',
                    'province_id' => 'required|numeric|exists:provinces,id',
                    // the follow up fees must be less than or equal the fees
                    'follow_up_fees' => 'required|numeric|lte:fees',
                ],$premium_case);
            } else {

                if (auth()->user()->is_premium == 1) {
                    $premium_case = [
                        'premium_fees' => 'required|numeric|min:1|lt:fees',
                        'premium_follow_up_fees' => 'required|numeric|min:1|lt:premium_fees',
                    ];
                } else {
                    $premium_case = [];
                }

                // in case of poly clinic
                if ($this->method() == 'POST') {
                    return array_merge([
                        'en_name' => ['required', new EnglishClinic()],
                        'ar_name' => ['required', new ArabicClinic()],
                        'pattern' => 'required|min:0|max:1',
                        'res_limit' => 'required|numeric|min:0',
                        'fees' => 'required|numeric|min:1',
                        // the follow up fees must be less than or equal the fees
                        'follow_up_fees' => 'required|numeric|lte:fees',
                        'speciality_id' => 'required|numeric|exists:specialities,id',
                        'province_id' => 'required|numeric|exists:provinces,id',
                        'avg_reservation_time' => 'required|numeric|min:0',
                        'reservation_deadline' => [
                            'required',
                            'date',
                            'before:' . Carbon::today("Africa/Cairo")->addDays(100)->format('Y-m-d')
                        ],
                    ],$premium_case);
                } else {
                    return array_merge([
                        'en_name' => ['required', new EnglishClinic()],
                        'ar_name' => ['required', new ArabicClinic()],
                        'pattern' => 'required|min:0|max:1',
                        'res_limit' => 'required|numeric|min:0',
                        'fees' => 'required|numeric|min:1',
                        // the follow up fees must be less than or equal the fees
                        'follow_up_fees' => 'required|numeric|max:' . $this->fees,
                        'speciality_id' => 'required|numeric|exists:specialities,id',
                        'province_id' => 'required|numeric|exists:provinces,id',
                        'avg_reservation_time' => 'required|numeric|min:0',
                        'reservation_deadline' => [
                            'required',
                            'date',
                            'before:' . Carbon::today('Africa/Cairo')->addDays(100)->format('Y-m-d')
                        ],
                    ],$premium_case);
                }
            }
        } else {
            return [
                'avg_reservation_time' => 'required|numeric|min:0',
                'reservation_deadline' => [
                    'required',
                    'date',
                    'before:' . Carbon::today('Africa/Cairo')->addDays(100)->format('Y-m-d')
                ],
            ];
        }
    }
}

