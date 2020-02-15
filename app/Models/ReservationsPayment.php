<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationsPayment extends Model
{
    protected $table = 'reservations_payment';

    protected $fillable = [
        'fees',
        'offer',
        'reservation_id',
        'promo',
        'discount',
        'total'
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function setFeesAttribute($fees)
    {
        $this->attributes['fees'] = $fees ?? 0;
    }

    public function setDiscountAttribute($discount)
    {
        $this->attributes['discount'] = $discount ?? 0;
    }
}
