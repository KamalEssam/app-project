<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'en_name',
        'ar_name',
        'country_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function accounts()
    {
        return $this->hasMany(Country::class);
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
            if (auth()->user()) {
                $model->created_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if (auth()->user()) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }
}
