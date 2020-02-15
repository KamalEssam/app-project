<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationStandBy extends Model
{

    protected $table = 'reservation_stand_by';

    protected $fillable = [
        'reservation_id',
        'clinic_id',
        'queue',
    ];


    protected $casts = [
        'reservation_id' => 'integer',
        'clinic_id' => 'integer',
        'queue' => 'integer',
    ];
}
