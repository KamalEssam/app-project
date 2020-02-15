<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorDetail extends Model
{
    protected $fillable = [
        'account_id',
        'speciality_id',
        'featured_rank',
        'en_bio',
        'ar_bio',
        'created_by',
        'updated_by',
        'en_reservation_message',
        'ar_reservation_message',
        'facebook',
        'twitter',
        'linkedin',
        'youtube',
        'googlepluse',
        'instagram',
        'website',
        'min_fees',
        'min_premium_fees',
        'max_hours_to_cancel_reservation'
    ];


    public function setEnBioAttribute($value)
    {
        if (is_null($value) || empty($value)) {
            $this->attributes['en_bio'] = "No Data To Show";
        } else {
            $this->attributes['en_bio'] = $value;
        }
    }

    public function setArBioAttribute($value)
    {
        if (is_null($value) || empty($value)) {
            $this->attributes['ar_bio'] = "لا توجد بيانات للعرض";
        } else {
            $this->attributes['ar_bio'] = $value;
        }
    }

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getUserIdAttribute($value)
    {
        return (int)$value;
    }

    public function getSpecialityIdAttribute($value)
    {
        return (int)$value;
    }

    public function getCreatedByAttribute($value)
    {
        return (int)$value;
    }

    public function getUpdatedByAttribute($value)
    {
        return (int)$value;
    }

    public function getMaxHoursToCancelReservationAttribute($value)
    {
        return (int)$value;
    }


    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
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
