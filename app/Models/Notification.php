<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'receiver_id',
        'sender_id',
        'multicast',
        'en_title',
        'ar_title',
        'en_message',
        'ar_message',
        'url',
        'object_id',
        'table',
        'is_read',
        'created_by',
    ];


    protected $appends = ['sender', 'receiver'];


    public function getSenderAttribute()
    {
        if (!$this->sender_id) {
            return new \stdClass();
        }
        $sender = User::where('id', $this->sender_id)->select('id', 'name', 'image')->first();
        return $sender;
    }

    public function getReceiverAttribute()
    {
        if (!$this->receiver_id) {
            return new \stdClass();
        }
        $sender = User::where('id', $this->receiver_id)->select('id', 'name', 'image')->first();
        return $sender;
    }

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getReceiverIdAttribute($value)
    {
        return (int)$value;
    }

    public function getSenderIdAttribute($value)
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
    public function getObjectIdAttribute($value)
    {
        return (int)$value;
    }


    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getNotificationById($notification_id)
    {
        $notification = Notification::find($notification_id);
        $notification->is_read = 1;
        $notification->update();
        if (!$notification) {
            return false;
        }
        return $notification;
    }
    /****************** boot method to set created by and updated by ******************/
    /****************** boot method to set created by and updated by ******************/
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(auth()->user()) {
                $model->created_by = auth()->user()->id;
            }
        });
    }
}
