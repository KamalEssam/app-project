<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorReservationPayment extends Model
{
    protected $table = 'doctor_reservation_payments';

    protected $fillable = [
        'account_id',
        'month',
        'year',
        'total'
    ];

    protected $casts = [
        'account_id' => 'integer',
        'total' => 'double',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
