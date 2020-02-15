<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueWith implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $account_id;

    public function __construct($account_id)
    {
        $this->account_id = $account_id;
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
        $user_exists = User::where('email', $value)->where('account_id', $this->account_id)
                                                   ->first();
        if($user_exists){
            return false;
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
        return 'email already exists for this account.';
    }
}
