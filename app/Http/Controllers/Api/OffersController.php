<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Web\OfferCategoryRepository;
use App\Http\Repositories\Web\OfferRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class OffersController extends ApiController
{
    private $offersRepository;

    /**
     * WorkingHourController constructor.
     * @param Request $request
     * @param OfferRepository $offerRepository
     */
    public function __construct(Request $request, OfferRepository $offerRepository)
    {
        $this->offersRepository = $offerRepository;
        $this->setLang($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllOffers(Request $request)
    {
        $is_featured = ($request->has('is_featured') && $request->is_featured == 1) ? 1 : 0;
        $offers = $this->offersRepository->ApiGetOffers($is_featured, $request);
        if (!$offers) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.offers'));
        }

        foreach ($offers as $index => $offer) {
            // attach doctor object to the offers service
            $user = (new AuthRepository())->getDoctorDateForOffer($offer->doctor_id);
            $offer->doctor = $user;    // get doctor object for each offer
            if ($offer->doctor) {
                $offer->doctor->name = $offer->doctor_name;    // replace doctor name by account name
            }
            $offer->expiry_date = Carbon::parse($offer->expiry_date)->diffInDays(now());
            $offers[$index] = $offer;
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.offers'), [], $offers);
    }

    /**
     *  offer details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOfferDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|numeric|exists:offers,id',
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }

        $offer = $this->offersRepository->ApiGetOneOffer($request->get('offer_id'));
        if (!$offer) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.offers'));
        }

        // attach doctor object to the offers service
        $user = (new AuthRepository())->getDoctorDateForOffer($offer->doctor_id);
        $offer->doctor = $user;    // get doctor object for each offer
        if ($offer->doctor) {
            $offer->doctor->name = $offer->doctor_name;    // replace doctor name by account name
        }
        $offer->expiry_date = Carbon::parse($offer->expiry_date)->diffInDays(now());

        return self::jsonResponse(true, self::CODE_OK, trans('lang.offers'), [], $offer);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function offerIncreaseViews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|exists:offers,id',
        ]);
        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }
        $offer_increment = $this->offersRepository->ApiIncreaseOffersViews($request->offer_id);
        if (!$offer_increment) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.offer_increment_failed'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.offer_increment_ok'));
    }

    /**
     *  get the offer categories list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function offer_categories(Request $request)
    {
        $categories = (new OfferCategoryRepository())->offer_categories($request);
        if (!$categories) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_offer_categories'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.offer_categories'), '', $categories);
    }
}
