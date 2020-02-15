<?php

namespace App\Models;


use App\Http\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{

    protected $table = 'gallery';

    protected $fillable = [
        'unique_id',
        'image',
    ];

    protected $casts = [
        'id' => 'integer',
        'image' => 'string',
        'unique_id' => 'string',
    ];


    // set image
    public function setImageAttribute($image)
    {
        if ($image) {
            $image_name = FileTrait::uploadFile($image, 'assets/images/profiles/' . $this->attributes['unique_id'] . '/');
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
        return asset('assets/images/profiles/' . $this->unique_id . '/' . $value);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
