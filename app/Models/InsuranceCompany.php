<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceCompany extends Model
{
    protected $table = 'insurance_companies';

    protected $fillable = [
        'en_name',
        'ar_name',
        'image'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function accounts() {
        return $this->belongsToMany(Account::class,'account_insurance');
    }
}
