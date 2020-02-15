<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class IsDoctor implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param $doctor_id
     */
    public function __construct($doctor_id)
    {
        $this->doctor_id = $doctor_id;

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
        $user = User::where('id', $this->doctor_id)->first();
        if (isset($user) && $user->role_id == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Role';
    }
}
