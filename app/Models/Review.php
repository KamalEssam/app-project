<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    protected $table = 'reviews';

    protected $fillable = [
        'reservation_id',
        'content',
        'rate',
        'account_id',
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'reservation_id' => 'integer',
        'account_id' => 'integer',
        'rate' => 'double',
    ];


    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
