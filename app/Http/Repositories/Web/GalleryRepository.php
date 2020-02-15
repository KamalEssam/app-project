<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\GalleryInterface;
use App\Models\Gallery;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class GalleryRepository implements GalleryInterface
{
    public $gallery;

    public function __construct()
    {
        $this->gallery = new Gallery();
    }


    /**
     *  get Doctor gallery
     *
     * @param $unique_id
     * @return mixed
     */
    public function getDoctorGallery($unique_id)
    {
        try {
            $gallery = $this
                ->gallery
                ->where('unique_id', $unique_id)
                ->orderBy('created_at', 'desc')
                ->get();
            if (!$gallery) {
                return false;
            }
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        return $gallery;
    }

    /**
     *  add new image for the gallery
     *
     * @param $image
     * @param $unique_id
     * @return bool
     */
    public function addImage($image, $unique_id)
    {
        try {
            $this->gallery->create([
                'unique_id' => $unique_id,
                'image' => $image
            ]);
        } catch (\Exception $e) {
            return ApiController::catchExceptions($e->getMessage());
        }

        return true;
    }
}
