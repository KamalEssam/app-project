<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

class EnglishClinic implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $notAllowed = ['d ', 'd.', 'dr ', 'doctor ', 'dr.', 'dr/', 'dr-', 'dr /'];

        foreach ($notAllowed as $item) {
            if (strpos(strtolower($value), $item) === 0) {
                \Log::info(strpos($value, $item) === 0);
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('lang.only_clinic_name_allowed');
    }
}
