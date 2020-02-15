<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'website',
        'email',
        'mobile',
        'twitter',
        'youtube',
        'googlepluse',
        'instagram',
        'user_counter',
        'assistant_counter',
        'account_counter',
        'created_by',
        'updated_by',
        'min_featured_stars',

        'en_about_us',
        'ar_about_us',

        'debug_mode'
    ];

    protected $casts = [
        'id' => 'integer',
        'user_counter' => 'integer',
        'assistant_counter' => 'integer',
        'account_counter' => 'integer',
        'min_featured_stars' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

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
