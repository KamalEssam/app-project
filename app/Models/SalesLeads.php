<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLeads extends Model
{
    protected $table = 'sales_leads';

    protected $fillable = [
        'name',
        'mobile',
        'status',
        'sale_id'
    ];

    protected $casts = [
        'sale_id' => 'integer'
    ];

    public function sale()
    {
        return $this->belongsTo(User::class);
    }
}
