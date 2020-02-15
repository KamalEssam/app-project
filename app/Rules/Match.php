<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Hash;

class Match implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $oldValue;

    public function __construct($oldValue)
    {
        $this->oldValue = $oldValue;
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
        if (Hash::check($value, $this->oldValue)) {
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
        return 'The value dosen\'t match any of our records.';
    }
}
