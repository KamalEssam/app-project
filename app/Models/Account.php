<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'unique_id',
        'ar_name',
        'en_name',
        'en_address',
        'ar_address',
        'due_amount',
        'due_date',
        'plan_id',
        'city_id',
        'type',
        'is_published',
        'is_completed',
        'created_by',
        'updated_by',
        'lat',
        'lng',
        'en_title',
        'ar_title'
    ];


    protected $casts = [
        'id' => 'integer',
        'unique_id' => 'string',
        'is_published' => 'integer',
        'is_completed' => 'integer',
        'plan_id' => 'integer',
        'city_id' => 'integer',
        'type' => 'integer',
        'lat' => 'double',
        'lng' => 'double',
        'due_amount' => 'double',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    public function usersWhoFavouriteMe()
    {
        return $this->belongsToMany(User::class);
    }

    public function getArAddressAttribute($value)
    {
        return $value ?? $value = trans('lang.not_set');
    }

    public function getEnAddressAttribute($value)
    {
        return $value ?? $value = trans('lang.not_set');
    }

    public function getAddressAttribute($value)
    {
        return $value ?? $value = trans('lang.not_set');
    }

    public function doctorDetail()
    {
        return $this->hasOne(DoctorDetail::class);
    }

    public function dueAmount()
    {
        return $this->hasOne(DueAmount::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function myRecommends()
    {
        return $this->belongsToMany(User::class, 'recommendation');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'unique_id', 'unique_id');
    }

    /**
     *  many to many relation between account and service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'account_service')
            ->withPivot('price', 'premium_price')
            ->withTimestamps();
    }

    // many to many relation
    public function subSpecialities()
    {
        return $this->belongsToMany(SubSpeciality::class, 'account_speciality')->withTimestamps();
    }

    // many to many relation
    public function insuranceCompanies()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'account_insurance')->withTimestamps();
    }

    // account cashback
    public function cashback()
    {
        return $this->hasMany(CashBack::class);

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
