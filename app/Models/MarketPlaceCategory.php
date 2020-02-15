<?php

namespace App\Models;

use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;

class MarketPlaceCategory extends Model
{
    protected $table = 'market_place_category';

    protected $fillable = [
        'ar_name',
        'en_name',
        'is_active',
        'image',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'integer',
    ];

    public function setImageAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/market-place-categories/');
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
        return asset('assets/images/market-place-categories/' . $image);
    }

    public function market()
    {
        return $this->hasMany(MarketPlace::class);
    }

    // SCOPES
    /*
    *   get active products
    *  @param $query
    *  @return mixed
    */
    public function scopeActive($query)
    {
        return $query->where('market_place_category.is_active', 1);
    }
}
