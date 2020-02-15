<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface GalleryInterface
{

    /**
     *  get Doctor gallery
     *
     * @param $unique_id
     * @return mixed
     */
    public function getDoctorGallery($unique_id);

}
