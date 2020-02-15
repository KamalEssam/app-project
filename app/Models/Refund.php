<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{

    protected $table = 'refund';

    protected $fillable = [
        'user_id',
        'reservation_id',
        'status',
        'condition'
    ];

    protected $casts = [
        'id' => 'integer',
        'reservation_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'condition' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
}
