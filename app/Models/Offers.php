<?php

namespace App\Models;

use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use URL;

class Offers extends Model
{

    use SoftDeletes;

    protected $table = 'offers';

    // softDelete
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'category_id',
        'ar_name',
        'en_name',
        'ar_desc',
        'en_desc',
        'doctor_id',
        'reservation_fees_included',
        'price_type',
        'old_price',
        'price',
        'views_no',
        'users_booked',
        'is_featured',
        'image',
        'expiry_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'doctor_id' => 'integer',
        'old_price' => 'double',
        'price' => 'double',
        'views_no' => 'integer',
        'users_booked' => 'integer',
        'is_featured' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(OffersCategories::class, 'category_id');
    }

    public function setImageAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/offers/');
            if ($image_name == true) {
                $this->attributes['image'] = $image_name;
            } else {
                // you can set default image or throw an error
                $this->attributes['image'] = 'default.png';
            }
        }
    }

    public function getImageAttribute($image)
    {
        if (URL::to('/') == "http://localhost:8000") {
            return asset('/assets/images/offers/' . $image);
        }
        return URL::to('/') . "/media/images/$image/image.png?width=[w]&height=[h]";
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'offers_services', 'offer_id', 'service_id')
            ->withTimestamps();
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
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
