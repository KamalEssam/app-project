<?php

namespace App\Http\Traits;

use Image;
use File;

trait FileTrait
{
    public static function uploadImage($file, $stringPath, $resizeWidth = null, $width = null, $height = null)
    {
        ini_set('upload_max_filesize', '10M');

        $image = $file;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imageLocation = getcwd() . '/' . $stringPath . '/' . $imageName;
        if ($resizeWidth == null && $width == null && $height == null) {
            Image::make($image)->save($imageLocation);
        } else {
            // Upload Image
            Image::make($image)->resize($resizeWidth, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->crop($width, $height)->save($imageLocation);
        }

        return $imageName;
    }


    public static function uploadFile($file, $stringPath)
    {
            if (!file_exists(getcwd() . '/' . $stringPath)) {
                if (!file_exists(getcwd() . '/' . $stringPath)) {
                    mkdir(getcwd() . '/' . $stringPath, 0777, true);
                }
            }

            $image = $file;
            $imageName = str_random(6) . '_' . time() . '.' . $image->getClientOriginalExtension();
            $imageLocation = getcwd() . '/' . $stringPath . '/';
            $image->move($imageLocation, $imageName);
            if (!$image) {
                return false;
            }
            return $imageName;
    }

    public static function uploadFile64($file, $stringPath)
    {
        $file = str_replace(array('data:image/jpeg;base64,', 'data:image/jpg;base64,', 'data:image/png;base64,', ' '), array('', '', '', '+'), $file);
        $filename = str_random(6) . '_' . time() . '.' . 'png'; // file name based on time
        $fileLocation = getcwd() . '/' . $stringPath . $filename;
        $file_uploaded = base64_decode($file);
        file_put_contents($fileLocation, $file_uploaded);
        if (!$file_uploaded) {
            return false;
        }
        return $filename;
    }

    public static function deleteFile($path)
    {
        File::delete(getcwd() . '/' . $path);
        return true;
    }

    public static function getProfileImage($user)
    {
        if ($user->image == NULL) {
            $image = url('assets\images\profiles\default.png');
        } elseif ($user->is_facebook == 1) {
            $image = url($user->image);
        } else {
            $image = url('assets\images\profiles/' . $user->image);
        }
        return $image;
    }
}
