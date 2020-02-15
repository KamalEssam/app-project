<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'en_name',
        'ar_name',
        'price_of_day',
        'no_of_clinics',
        'en_desc',
        'ar_desc',
        'image',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'no_of_clinics' => 'integer',
        'price_of_day' => 'double',
    ];


    public function accounts()
    {
        return $this->hasMany(Account::class);
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
