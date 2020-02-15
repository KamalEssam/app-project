<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'desc'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
