<?php

namespace App\Http\Requests;
use App\Rules\Match;
use App\Rules\PasswordMatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends FormRequest
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
            'old' => ['required', new Match(auth()->user()->password)],
            'new' => 'required',
            'confirm' => ['required', new PasswordMatch($this->new)],
        ];
    }
}
