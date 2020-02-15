<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPlan extends Model
{
    protected $table = 'patients_plans';
    protected $fillable = [
        'en_name',
        'ar_name',
        'price',
        'months',
        'en_desc',
        'ar_desc',
        'points',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'points' => 'integer',
        'updated_by' => 'integer',
        'months' => 'integer',
        'price' => 'double',
    ];

    protected $appends = ['currency', 'title'];

    public function getCurrencyAttribute()
    {
        return strtoupper(trans('lang.egp'));
    }

    public function users()
    {
        return $this->belongsToMany(PatientPlan::class);
    }

    public function getTitleAttribute()
    {
        if (app()->getLocale() == 'en') {
            return $this->price . ' Egp / ' . $this->months . ' months';
        }

        return $this->price . ' جنيه / ' . $this->months . ' شهر';
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
