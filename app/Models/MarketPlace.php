<?php

namespace App\Models;

use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;

class MarketPlace extends Model
{
    protected $table = 'market_places';

    protected $fillable = [
        'ar_title',
        'en_title',
        'ar_desc',
        'en_desc',
        'points',
        'image',
        'is_active',
        'max_redeems',
        'brand_id',
        'price',
        'redeem_expiry_days',
        'market_place_category_id',
    ];

    protected $casts = [
        'is_active' => 'integer',
        'max_redeems' => 'integer',
        'brand_id' => 'integer',
        'redeem_expiry_days' => 'integer',
        'market_place_category_id' => 'integer',
    ];

    public function setImageAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/market-place/');
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
        return asset('assets/images/market-place/' . $image);
    }

    public function brand()
    {
        return $this->belongsTo(User::class);
    }

    public function redeems()
    {
        return $this->belongsToMany(User::class, 'user_market_places', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(MarketPlaceCategory::class);
    }

    // SCOPES
    /*
    *   get active products
    *  @param $query
    *  @return mixed
    */
    public function scopeActive($query)
    {
        return $query->where('market_places.is_active', 1);
    }

    /*
 *   get active products
 *  @param $query
 *  @return mixed
 */
    public function scopeNotexpired($query)
    {
        return $query->where('market_places.max_redeems', '>=', 1);
    }
}
