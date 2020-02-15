<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Web\AdRepository;
use Illuminate\Http\Request;

class AdsController extends ApiController
{
    private $adsRepository;

    /**
     * WorkingHourController constructor.
     * @param Request $request
     * @param AdRepository $adsRepository
     */
    public function __construct(Request $request, AdRepository $adsRepository)
    {
        $this->adsRepository = $adsRepository;
        $this->setLang($request);
    }

    /**
     *  get slider for web
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adsSlider()
    {
        $sliders = $this->adsRepository->getSlidesWithOffers();
        if (!$sliders) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.ad_not_found'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.ads'), '', $sliders);
    }

    /**
     *  get slider for mobile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adsMobileSlider()
    {
        $sliders = $this->adsRepository->getSlides();
        if (!$sliders) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.ad_not_found'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.ads'), '', $sliders);
    }
}
