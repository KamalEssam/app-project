<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'en_name',
        'ar_name',
        'speciality_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function specaility()
    {
        return $this->belongsTo(Speciality::class);
    }

    public function service_account()
    {
        return $this->belongsToMany(Account::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function offers()
    {
        return $this->belongsToMany(Offers::class, 'offers_services');
    }

    /****************** boot method to set created by and updated by ******************/
    public static function boot()
    {
        parent::boot();
        $auth_user = auth()->user();
        if ($auth_user) {
            static::creating(function ($model) use ($auth_user) {
                $model->created_by = $auth_user->id;
            });
            static::updating(function ($model) use ($auth_user) {
                $model->updated_by = $auth_user->id;
            });
        }
    }
}
