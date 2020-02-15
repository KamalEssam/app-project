<?php

namespace App\Models;

use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;

class OffersCategories extends Model
{
    protected $table = 'offers_categories';
    protected $fillable = [
        'ar_name',
        'en_name',
        'image',
        'speciality_id',
    ];

    public function setImageAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/offer_categories/');
            if ($image_name == true) {
                $this->attributes['image'] = $image_name;
            } else {
                // you can set default image or throw an error
                $this->attributes['image'] = 'default.png';
            }
        }
    }

    // get the full path of profile image
    public function getImageAttribute($value)
    {
        if ($value == 'default.png') {
            return asset('assets/images/' . $value);
        }

        return asset('assets/images/offer_categories/' . $value);
    }

    public function offers()
    {
        return $this->hasMany(Offers::class, 'category_id');
    }

    public function specilaity()
    {
        return $this->belongsTo(Speciality::class);
    }
}
