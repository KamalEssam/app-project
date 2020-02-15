<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{

    protected $fillable = [
        'day',
        'ar_reason',
        'en_reason',
        'clinic_id',
        'created_by',
        'day_index',
    ];

    protected $casts = [
        'id' => 'integer',
        'en_reason' => 'string',
        'ar_reason' => 'string',
        'clinic_id' => 'integer',
        'created_by' => 'integer',
        'day_index' => 'integer'
    ];

    public function clinic()
    {
        return $this->hasMany(Clinic::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    }
}
