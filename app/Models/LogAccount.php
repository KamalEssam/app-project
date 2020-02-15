<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAccount extends Model
{

    protected $table = 'sales_created_accounts';

    protected $fillable = [
        'sales_id',
        'account_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'sales_id' => 'integer',
        'account_id' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class,'sales_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
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
