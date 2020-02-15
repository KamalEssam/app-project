<?php

namespace App\Models;

use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'en_title',
        'ar_title',
        'en_desc',
        'ar_desc',
        'screen_shot',
        'background',
        'type',
        'clicks',
        'offer_id',
        'doctor_id',
        'is_active',
        'time_from',
        'time_to',
        'date_from',
        'date_to',
        'slide',
        'priority',
    ];

    protected $casts = [
        'id' => 'integer',
        'slide' => 'integer',
        'priority' => 'integer',
        'is_active' => 'integer',
        'type' => 'integer',
    ];

    public function setBackgroundAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/ads/background/');
            if ($image_name == true) {
                $this->attributes['background'] = $image_name;
            } else {
                // you can set default image or throw an error
                $this->attributes['background'] = 'default.png';
            }
        }
    }

    public function getBackgroundAttribute($image)
    {
        return asset('assets/images/ads/background/' . $image);
    }


    public function setScreenShotAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/ads/screen_shot/');
            if ($image_name == true) {
                $this->attributes['screen_shot'] = $image_name;
            } else {
                // you can set default image or throw an error
                $this->attributes['screen_shot'] = 'default.png';
            }
        }
    }

    public function getScreenShotAttribute($image)
    {
        return asset('assets/images/ads/screen_shot/' . $image);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offers::class);
    }
}
