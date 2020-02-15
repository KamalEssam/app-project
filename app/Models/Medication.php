<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = [
        'name',
        'company',
        'dosage',
        'dosage_form',
        'sub_indication',
        'indication',
        'price',
        'active_ingredient',
        'is_pushed',
        'created_by',
        'updated_by',
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getPriceAttribute($value)
    {
        return (double)$value;
    }

    public function getCreatedByAttribute($value)
    {
        return (int)$value;
    }

    public function getUpdatedByAttribute($value)
    {
        return (int)$value;
    }





    public function visits()
    {
        return $this->belongsToMany(Visit::class);
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
