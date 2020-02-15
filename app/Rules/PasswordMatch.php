<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordMatch implements Rule
{

    public $newPassword;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($value == $this->newPassword){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Password doesn\'t match.';
    }
}
