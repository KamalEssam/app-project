<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBack extends Model
{

    protected $table = 'cash_back';

    protected $fillable = [
        'account_id',
        'patient_id',
        'clinic_id',
        'reservation_id',
        'is_approved',
        'patient_cash',
        'doctor_cash',
        'seena_cash',
        'is_paid',
    ];

    protected $casts = [
        'id' => 'integer',
        'patient_cash' => 'double',
        'doctor_cash' => 'double',
        'seena_cash' => 'double',
        'account_id' => 'integer',
        'patient_id' => 'integer',
        'clinic_id' => 'integer',
    ];


    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
