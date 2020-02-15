<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'diagnosis',
        'clinic_id',
        'prescriptions',
        'next_visit',
        'reservation_id',
        'created_by',
        'updated_by'
    ];


    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'clinic_id' => 'integer',
        'type' => 'integer',
        'reservation_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function medications()
    {
        return $this->belongsToMany(Medication::class);
    }
    /****************** boot method to set created by and updated by ******************/
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(auth()->user()) {
                $model->created_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if(auth()->user()) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }
}
