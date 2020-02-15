<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'visit_id',
        'comment',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getVisitIdAttribute($value)
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

    public function visit()
    {
        return $this->belongsTo(Visit::class);
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
