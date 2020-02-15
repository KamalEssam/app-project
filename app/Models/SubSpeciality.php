<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSpeciality extends Model
{
    protected $fillable = [
        'ar_name',
        'en_name',
        'speciality_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'en_name' => 'string',
        'ar_name' => 'string',
        'speciality_id' => 'integer'
    ];

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    public function account()
    {
        return $this->belongsToMany(Account::class, 'account_speciality');
    }
}
