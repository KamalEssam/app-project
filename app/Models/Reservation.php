<?php

namespace App\Models;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Api\AuthRepository;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Reservation extends Model
{

    protected $fillable = [
        'user_id',
        'working_hour_id',
        'clinic_id',
        'queue',
        'day',
        'status',
        'type',
        'complaint',
        'check_in',
        'check_out',
        'created_by',
        'updated_by',
        'payment_method',
        'transaction_id',
        'offer_id',
        'reservation_ignore',
        'cashback_status',
        'promo_code_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'clinic_id' => 'integer',
        'user_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'transaction_id' => 'integer',
        'working_hour_id' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'reservation_ignore' => 'integer',
    ];

    public function setWorkingHourIdAttribute($value)
    {
        $this->attributes['working_hour_id'] = ($value == -1 || $value == 0) ? null : (int)$value;
    }

    public function workingHour()
    {
        return $this->belongsTo(WorkingHour::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function visit()
    {
        return $this->hasOne(Visit::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    // reservation cashback
    public function cashback()
    {
        return $this->belongsTo(CashBack::class);

    }


    /**
     *  get get user reservations
     *
     * @param null $status
     * @param null $day
     * @param null $name
     * @param null $clinic
     * @param null $object_id
     * @return mixed
     */
    public static function getReservationsByStatus($status = null, $day = null, $name = null, $clinic = null, $object_id = null)
    {
        if ($clinic != null) {
            // if clinic work with intervals
            if ($clinic->pattern == WebController::PATTERN_INTERVAL) {
                $reservations = DB::table('reservations')
                    ->leftjoin('working_hours', 'reservations.working_hour_id', 'working_hours.id')
                    ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                    ->join('users', 'reservations.user_id', 'users.id')
                    ->where(function ($query) use ($status, $day, $name, $object_id) {
                        self::getReservationsByStatusFilters($query, $status, $day, $name, $object_id);
                    })
                    ->select('reservations.*', 'users.name', 'working_hours.time', DB::raw('DATE_FORMAT(reservations.day, "%Y-%m-%d") as day'))
                    ->orderBy('reservations.created_at', 'desc')
                    ->paginate(13);

                return $reservations;

            } // if clinic work with queue

            $reservations = DB::table('reservations')
                ->join('clinics', 'reservations.clinic_id', 'clinics.id')
                ->join('users', 'reservations.user_id', 'users.id')
                ->where(function ($query) use ($status, $day, $name, $object_id) {
                    self::getReservationsByStatusFilters($query, $status, $day, $name, $object_id);
                })
                ->where('reservations.clinic_id', $clinic->id)
                ->select('reservations.*', 'users.name', DB::raw('DATE_FORMAT(reservations.day, "%Y-%m-%d") as day'))
                ->orderBy('reservations.created_at', 'desc')
                ->paginate(13);
            return $reservations;
        }
        return false;
    }

    /**
     * when reservations have expired day or time convert to missed
     *
     * @return int
     */
    public static function setMissedReservation()
    {
        return DB::table('reservations')
            ->where('reservations.day', '<', Carbon::now("Africa/Cairo")->format("Y-m-d"))
            ->where('reservations.status', WebController::R_STATUS_APPROVED)
            ->update(array(
                'reservations.status' => WebController::R_STATUS_MISSED,
            ));
    }

    /**
     *  filter for the above query for getReservationsByStatus
     *
     * @param $query
     * @param $status
     * @param $day
     * @param $name
     * @param $object_id
     */
    public static function getReservationsByStatusFilters($query, $status, $day, $name, $object_id)
    {
        if (is_array($status)) {
            // in case more than reservation
            $query->whereIn('reservations.status', $status);
        } else {
            if ($status != 'all') {
                $query->where('reservations.status', '=', $status);
            }
        }

        // in case we specified a date
        if (($day != null) && ($day != 0)) {
            $query->where('reservations.day', '=', $day);
        }

        // in case we specified a user name
        if (($name != null) && ($name != '')) {
            $query->where('users.name', 'like', '%' . $name . '%');
        }

        // in case we specified reservation id
        if ($object_id != null) {
            $query->where('reservations.id', '=', $object_id);
        }
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

    /**
     *
     *  Scopes
     *
     */
    public function scopeToday($query)
    {
        return $query->where('day', '=', Carbon::now("Africa/Cairo")->format("Y-m-d"));
    }
}
