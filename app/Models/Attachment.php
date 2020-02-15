<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Super;

class Attachment extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'attachment',
        'type',
        'created_by',
        'updated_by',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getUserIdAttribute($value)
    {
        return (int)$value;
    }
    public function getTypeAttribute($value)
    {
        return (int)$value;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
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
