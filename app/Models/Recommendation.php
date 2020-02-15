<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $table = 'recommendation';

    protected $fillable = [
        'user_id',
        'account_id',
        'receiver_serial'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getUserIdAttribute($value)
    {
        return (int)$value;
    }
    public function getAccountIdAttribute($value)
    {
        return (int)$value;
    }
}
