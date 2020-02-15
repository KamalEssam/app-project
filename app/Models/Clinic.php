<?php

namespace App\Models;

use App\Http\Traits\DateTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use DateTrait;
    protected $appends = ['working_hours_start_end'];

    protected $fillable = [
        'en_address',
        'ar_address',
        'en_name',
        'ar_name',
        'lat',
        'lng',
        'mobile',
        'pattern',
        'created_by',
        'updated_by',
        'avg_reservation_time',
        'reservation_deadline',
        'fees',
        'res_limit',
        'account_id',
        'follow_up_fees',
        'vat_included',
        'speciality_id',
        'province_id',
        'premium_fees',
        'premium_follow_up_fees'
    ];

    // set image
    public function setReservationDeadlineAttribute($value)
    {
        $this->attributes['reservation_deadline'] = Carbon::parse($value)->diffInDays(Carbon::now()) + 1;;
    }

    public function getWorkingHoursStartEndAttribute()
    {
        $working_hours = WorkingHour::where('clinic_id', $this->id)->where('deleted_at', NULL)->selectRaw('min(time) as start, max(time) as end')->first();
        if (empty($working_hours->start) && empty($working_hours->end)) {
            $working_hours_start_end = '';
        } else {
            $working_hours_start_end = self::getTimeByFormat($working_hours->start, 'h:i ') . trans('lang.' . self::getTimeByFormat($working_hours->start, 'A')) . ' ' . trans('lang.to') . ' ' . self::getTimeByFormat($working_hours->end, 'h:i ') . trans('lang.' . self::getTimeByFormat($working_hours->end, 'A'));
        }
        return $working_hours_start_end;
    }


    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'fees' => 'double',
        'pattern' => 'integer',
        'avg_reservation_time' => 'integer',
        'follow_up_fees' => 'integer',
        'vat_included' => 'integer',
        'account_id' => 'integer',
        'speciality_id' => 'integer',
        'province_id' => 'integer',
        'premium_fees' => 'double',
        'premium_follow_up_fees' => 'double'
    ];

    public function getNameAttribute($value)
    {
        return $value = ($value === null) ? $value = " " : $value;
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function clinicQueue()
    {
        return $this->hasOne(ClinicQueue::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function temperory_reservations()
    {
        return $this->hasMany(TemporaryReservation::class);
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
