<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redeems extends Model
{
    protected $table = 'user_market_places';

    protected $fillable = [
        'user_id',
        'product_id',
        'is_used',
        'expiry_date',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer',
        'is_used' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(MarketPlace::class);
    }
}
