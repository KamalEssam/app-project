<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumPromoCodes extends Model
{
    protected $table = 'premium_promo_codes';

    protected $fillable = [
        'code',
        'influencer_id',
        'owner_id',
        'expiry_date',
        'discount_type',
        'discount',
        'created_by',
        'updated_by',
    ];


    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'influencer_id' => 'integer',
        'owner_id' => 'integer',
        'expiry_date' => 'date',
        'discount_type' => 'integer',
        'discount' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function infuencer() {
        return $this->belongsTo(Influencers::class,'influencer_id');
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
