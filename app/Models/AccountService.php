<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountService extends Model
{
    protected $table = 'account_service';

    protected $fillable = [
        'account_id',
        'service_id',
        'price',
        'premium_price'
    ];

    protected $casts = [
        'account_id' => 'integer',
        'service_id' => 'integer',
        'price' => 'double',
        'premium_price' => 'double',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
