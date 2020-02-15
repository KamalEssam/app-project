<?php

namespace App\Models;

use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;
use URL;

class Speciality extends Model
{
    protected $fillable = [
        'en_speciality',
        'ar_speciality',
        'is_featured',
        'image',
        'slug',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];


    // get the full path of profile image
    public function getImageAttribute($value)
    {
        if ($value == 'default.png') {
            return asset('assets/images/' . $value);
        }
        return asset('assets/images/specialities/' . $value);
    }

    public function setImageAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/specialities/');
            if ($image_name == true) {
                $this->attributes['image'] = $image_name;
            } else {
                // you can set default image or throw an error
                $this->attributes['image'] = 'default.png';
            }
        }
    }

    // set slug
    public function setEnSpecialityAttribute($value)
    {
        $this->attributes['en_speciality'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }

    public function subSpecialities()
    {
        return $this->hasMany(SubSpeciality::class);
    }

    public function doctorDetails()
    {
        return $this->hasMany(DoctorDetail::class);
    }

    public function sponseredDoctors()
    {
        // get all featured images
        return $this->hasMany(DoctorDetail::class)->where('featured_rank', '>=', '1');
    }

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function offerCategories()
    {
        return $this->hasMany(OffersCategories::class);
    }

    function offers()
    {
        return $this->hasManyThrough(Offers::class, OffersCategories::class,
            'speciality_id', 'category_id');
    }
}
