<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\OfferRepository;
use App\Http\Requests\OfferRequest;
use DB;

class OffersController extends WebController
{
    private $offerRepo;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepo = $offerRepository;
    }

    /**
     *  show list of all offers
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $offers = $this->offerRepo->getAllOffers();
        return view('admin.rk-admin.offers.index', compact('offers'));
    }

    /**
     *  show the offer details in separate page
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $offer = $this->offerRepo->getOfferById($id);
        if (!$offer) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_offers'));
        }
        return view('admin.rk-admin.offers.show', compact('offer'));
    }

    /**
     * Store new offer in database
     *
     * @param OfferRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(OfferRequest $request)
    {
        if (!$request->has('is_featured')) {
            $request['is_featured'] = 0;
        }

        if (!$request->has('reservation_fees_included')) {
            $request['reservation_fees_included'] = 0;
        }

        // add offer category
        DB::beginTransaction();
        try {
            $offer = $this->offerRepo->createOffer($request);
            $offer->services()->sync($request['services']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.offer_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.offer_add_ok'), 'offers.index');
    }

    /**
     *  show edit offer
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $offer = $this->offerRepo->getWebOfferById($id);
        if (!$offer) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_offers'));
        }

        return view('admin.rk-admin.offers.edit', compact('offer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OfferRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(OfferRequest $request, $id)
    {
        $offer = $this->offerRepo->getWebOfferById($id);
        if (!$offer) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_offers'));
        }

        if (!$request->has('is_featured')) {
            $request['is_featured'] = 0;
        }

        if (!$request->has('reservation_fees_included')) {
            $request['reservation_fees_included'] = 0;
        }

        DB::beginTransaction();
        // update offer data
        try {
            $this->offerRepo->updateOffer($offer, $request);
            $offer->services()->sync($request['services']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.offer_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.offer_update_ok'), 'offers.index');
    }

    /**
     * Remove offer
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->offerRepo->offer, $id);
    }
}
