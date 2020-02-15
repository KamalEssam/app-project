<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\GalleryRepository;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends WebController
{
    private $galleryRepository;

    public function __construct(GalleryRepository $gallery)
    {
        $this->galleryRepository = $gallery;
    }

    /**
     *  show list of photo gallery
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $gallery = $this->galleryRepository->getDoctorGallery(auth()->user()->unique_id);
        return view('admin.doctor.gallery.index', compact('gallery'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        // add ad
        DB::beginTransaction();
        try {
            $images = $request['image'];
            $unique_id = auth()->user()->unique_id;
            foreach ($images as $image) {
                $this->galleryRepository->addImage($image, $unique_id);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.ad_image_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.image_add_ok'), 'gallery.index');
    }

    /**
     * Remove Gallery
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->galleryRepository->gallery, $id);
    }
}
