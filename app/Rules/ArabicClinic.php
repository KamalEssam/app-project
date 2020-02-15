<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

class ArabicClinic implements Rule
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
        $notAllowed = ['د ', 'دكتور', 'د/', 'د.','الدكتور','الاطباء'];

        foreach ($notAllowed as $item) {
            if (strpos($value, $item) === 0) {
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
