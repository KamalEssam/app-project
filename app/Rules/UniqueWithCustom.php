<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class UniqueWithCustom implements Rule
{
    private $table = '';
    private $col1 = '';
    private $col2 = '';
    private $val1 = '';
    private $val2 = '';
    private $count = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table, $count, $col1, $val1, $col2, $val2, $col3 = 1, $val3 = 1)
    {
        $this->table = $table;
        $this->count = $count;

        $this->col1 = $col1;
        $this->col2 = $col2;
        $this->col3 = $col3;

        $this->val1 = $val1;
        $this->val2 = $val2;
        $this->val3 = $val3;
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

        if ($this->count == 2) {

            $row = DB::table($this->table)
                ->where($this->col1, $this->val1)
                ->where($this->col2, $this->val2)
                ->first();
        } else {
            $row = DB::table($this->table)
                ->where($this->col1, $this->val1)
                ->where($this->col2, $this->val2)
                ->where($this->col3, $this->val3)
                ->first();
        }


        if (!is_null($row)) {
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
        return 'The ' . $this->col1 . ' ' . $this->val1 . ' is already exists';
    }
}
