<?php

namespace App\Models;

use App\Http\Controllers\WebController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'unique_id',
        'account_id',
        'name',
        'email',
        'password',
        'address',
        'mobile',
        'image',
        'gender',
        'birthday',
        'google_id',
        'is_active',
        'role_id',
        'lang',
        'clinic_id',
        'created_by',
        'facebook_id',
        'updated_by',
        'height',
        'weight',
        'last_notification_click',
        'percentage',
        'pin',
        'is_notification',
        'is_premium',
        'user_plan_id',
        'points',
        'cash_back',
        'login_counter'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'password', 'remember_token'
    ];

    protected $casts = [
        'id' => 'integer',
        'points' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'is_user' => 'integer',
        'gender' => 'integer',
        'role_id' => 'integer',
        'is_facebook' => 'integer',
        'is_active' => 'integer',
        'user_plan_id' => 'integer',
        'account_id' => 'integer',
        'clinic_id' => 'integer',
        'percentage' => 'integer',
        'is_premium' => 'integer',
        'login_counter' => 'integer',
        'height' => 'double',
        'weight' => 'double',
        'cash_back' => 'double',
        'unique_id' => 'string',
    ];

    /******************************************
     *               RELATIONS
     *             model relations
     * /*****************************************/
    public function myFavouriteDoctors()
    {
        return $this->belongsToMany(Account::class)
            ->withPivot('active')
            ->withTimestamps();
    }


    // set image
    public function setImageAttribute($value)
    {
        ($value == null || $value == "") ? $this->attributes['image'] = 'default.png' : $this->attributes['image'] = $value;
    }

    // get the full path of profile image
    public function getImageAttribute($value)
    {
        if ($value == 'default.png') {
            return asset('assets/images/' . $value);
        } elseif (strpos($value, 'facebook') !== false || strpos($value, 'google') !== false) {
            return $value;
        }
        return asset('assets/images/profiles/' . $this->unique_id . '/' . $value);
    }

    public function getBirthdayAttribute($value)
    {
        return $value ?? $value = trans('lang.not_set');
    }

    public function getEmailAttribute($value)
    {
        return $value ?? $value = ' ';
    }

    public function getAddressAttribute($value)
    {
        return $value ?? $value = trans('lang.not_set');
    }

    public function getWeightAttribute($value)
    {
        return $value ?? $value = 0;
    }

    public function getHeightAttribute($value)
    {
        return $value ?? $value = 0;
    }

    public function getGenderAttribute($value)
    {
        return $value ?? $value = 0;
    }

    public function setPasswordAttribute($value)
    {
        if ($value != null) {
            $this->attributes['password'] = bcrypt($value);
        }
    }


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function doctorDetail()
    {
        return $this->hasOne(DoctorDetail::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userPlan()
    {
        return $this->belongsTo(PatientPlan::class)->select('id', 'id as plan_id', app()->getLocale() . '_name as name', app()->getLocale() . '_desc as desc', 'price', 'months');
    }

    //check if user has given Rule
    public function hasRole($role)
    {
        return $this->role->name == $role;
    }

    //check if user has one  given Rules
    public function hasAnyRoles($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    /****************************created,updated functions*************************************/
    public function abouts()
    {
        return $this->hasMany(About::class);
    }

    public function redeem()
    {
        return $this
            ->belongsToMany(MarketPlace::class, 'user_market_places', 'user_id', 'product_id')
            ->withPivot('is_used', 'expiry_date')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function doctorDetails()
    {
        return $this->hasMany(DoctorDetail::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function permissionRoles()
    {
        return $this->hasMany(PermissionRole::class);
    }

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function specialities()
    {
        return $this->hasMany(Speciality::class);
    }


    public function dueAmounts()
    {
        return $this->hasMany(DueAmount::class);
    }

    public function recommendation()
    {
        return $this->belongsToMany(Account::class, 'recommendation');
    }


    // user premium promo-codes

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promoCodes()
    {
        return $this->belongsToMany(PremiumPromoCodes::class, 'premium_promo_codes_users');
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


    /**************************************** scopes **************************************
     *  sales role
     * @param $query
     * @return mixed
     */
    public function scopeSales($query)
    {
        return $query->where('role_id', WebController::ROLE_RK_SALES);
    }

    /**
     *  brand role
     *
     * @param $query
     * @return mixed
     */
    public function scopeBrand($query)
    {
        return $query->where('role_id', WebController::ROLE_BRAND);
    }

}
