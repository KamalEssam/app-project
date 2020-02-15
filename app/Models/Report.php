<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'body',
        'user_id',
        'reservation_id',
        'status',
        'type',
    ];

    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'reservation_id' => 'integer',
        'user_id' => 'integer',
    ];


    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
