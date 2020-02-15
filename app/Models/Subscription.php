<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'email'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }
}
