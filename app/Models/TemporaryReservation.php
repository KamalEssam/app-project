<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TemporaryReservation extends Model
{
    protected $table = 'reservation_temps';
    protected $fillable = [
        'doctor_id',
        'device_token',
        'clinic_id'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'doctor_id' => 'integer',
        'clinic_id' => 'integer',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
