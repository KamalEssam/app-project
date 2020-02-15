<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHour extends Model
{
    use SoftDeletes;

//    protected $appends = ['formatted_time'];

    protected $fillable = [
        'id',
        'time',
        'day',
        'start_date',
        'expiry_date',
        'clinic_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'clinic_id' => 'integer',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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
