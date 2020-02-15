<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeenaIncome extends Model
{

    protected $table = 'seena_income';


    protected $fillable = [
        'request_id',
        'account_id',
        'income',
    ];

    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer',
        'account_id' => 'integer',
        'income' => 'double',
    ];

    public function doctor()
    {
        return $this->belongsTo(Account::class);
    }

    public function request()
    {
        return $this->belongsTo(CashBack::class);
    }
}
