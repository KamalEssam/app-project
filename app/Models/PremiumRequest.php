<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumRequest extends Model
{
    protected $table = 'premium_request';

    protected $fillable = [
        'user_id',
        'plan_id',
        'approval',
        'created_by',
        'updated_by',
        'promo_code_id',
        'due_amount',
        'transaction_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'plan_id' => 'integer',
        'approval' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'promo_code_id' => 'integer',
        'due_amount' => 'double'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(PatientPlan::class);
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
