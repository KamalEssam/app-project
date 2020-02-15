<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Influencers extends Model
{
    protected $table = 'influencers';
    protected $fillable = [
        'en_name',
        'ar_name',
    ];

    public function promoCodes()
    {
        return $this->hasMany(PremiumPromoCodes::class);
    }
}
