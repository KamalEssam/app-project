<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'en_name',
        'ar_name',
        'type',
        'en_condition',
        'ar_condition',
        'created_by',
        'updated_by'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getTypeAttribute($value)
    {
        return (int)$value;
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
