<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketPlaceCategoryRequest extends FormRequest
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
        // create
        if ($this->method() == 'POST') {
            return [
                'en_name' => 'required',
                'ar_name' => 'required',
                'image' => 'required|image|mimes:jpeg,jpg,png|dimensions:min_width=500,min_height=500|dimensions:ratio=1/1'
            ];
        }

        // update
        return [
            'en_name' => 'required',
            'ar_name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|dimensions:min_width=500,min_height=500|dimensions:ratio=1/1'
        ];
    }
}
