<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AdRepository;
use App\Http\Requests\AdRequest;
use App\Http\Requests\CityRequest;
use DB;

class AdsController extends WebController
{
    private $adRepository;

    public function __construct(AdRepository $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    /**
     *  show list of all ads
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $ads = $this->adRepository->getAds();
        return view('admin.rk-admin.ads.index', compact('ads'));
    }

    /**
     * Store new ad in database
     *
     * @param AdRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(AdRequest $request)
    {

        if ($request->get('type') == 0) {
            unset($request['doctor_id']);
        } else {
            unset($request['offer_id']);
        }

        if (!$request->has('is_active')) {
            $request['is_active'] = 0;
        }

        // add ad
        DB::beginTransaction();
        try {
            $this->adRepository->createAd($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.ad_add_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.ad_add_ok'), 'ads.index');
    }

    /**
     *  show edit ad form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $ad = $this->adRepository->getAdById($id);
        if (!$ad) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.ad_not_found'));
        }
        return view('admin.rk-admin.ads.edit', compact('ad'));
    }

    /**
     *  Update the specified ad in database.
     *
     * @param AdRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AdRequest $request, $id)
    {
        $ad = $this->adRepository->getAdById($id);
        if (!$ad) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.ad_not_found'));
        }

        if ($request->get('type') == 0) {
            unset($request['doctor_id']);
        } else {
            unset($request['offer_id']);
        }

        if (!$request->has('is_active')) {
            $request['is_active'] = 0;
        }

        DB::beginTransaction();
        // update ad data
        try {
            $this->adRepository->updateAd($ad, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.ad_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.ad_update_ok'), 'ads.index');
    }

    /**
     * Remove ad
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->adRepository->ad, $id);
    }
}
