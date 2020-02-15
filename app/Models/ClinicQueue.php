<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ClinicQueue extends Model
{
    protected $fillable = [
        'clinic_id',
        'queue',
        'queue_status'
    ];

    protected $casts = [
        'id' => 'integer',
        'clinic_id' => 'integer',
        'queue' => 'integer',
        'queue_status' => 'integer',
    ];

    public function clinic()
    {
        return $this->hasOne(Clinic::class);
    }


    /**
     *   Local Scope
     *
     *
     * */


    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('updated_at', '=', Carbon::now('Africa/Cairo')->format('Y-m-d'));
    }

}
