<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisteredDoctors extends Model
{
    protected $table = 'account_user';

    protected $fillable = [
        'account_id',
        'user_id',
        'active'
    ];

    protected $casts = [
        'id' => 'integer',
        'account_id' => 'integer',
        'user_id' => 'integer',
        'active' => 'integer',
    ];
}
