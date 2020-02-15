<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DueAmount extends Model
{
    protected $fillable = [
        'account_id',
        'due_amount',
        'amount_paid',
        'created_by',
        'updated_by',
    ];


    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getDueAmountAttribute($value)
    {
        return (double)$value;
    }

    public function getAmountPaidAttribute($value)
    {
        return (double)$value;
    }

    public function getAccountIdAttribute($value)
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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(){
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
