<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationServices extends Model
{
    protected $fillable = [
        'reservation_id',
        'price',
        'ar_name',
        'en_name',
    ];

    protected $casts = [
        'id' => 'integer',
        'reservation_id' => 'integer',
        'price' => 'double'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
