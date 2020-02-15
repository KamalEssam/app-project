<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\MarketPlaceRepository;
use App\Http\Requests\MarketPlaceRequest;
use DB;

class MarketPlaceController extends WebController
{
    private $marketPlace;

    public function __construct(MarketPlaceRepository $marketPlace)
    {
        $this->marketPlace = $marketPlace;
    }

    /**
     *  show list of all market-places products
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $products = $this->marketPlace->getAllMarketPlaces();
        return view('admin.rk-admin.market-place.products.index', compact('products'));
    }

    /**
     * Show the form for creating a market place.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // get all countries
        $brands = (new AuthRepository())->getAllBrands();
        return view('admin.rk-admin.market-place.products.create', compact('brands'));
    }

    /**
     * Store new market-place in database
     *
     * @param CityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(MarketPlaceRequest $request)
    {
        // for is_active checkBox
        if (!$request->has('is_active')) {
            $request['is_active'] = 0;
        }

        // add market place
        DB::beginTransaction();
        try {
            $this->marketPlace->createMarketPlace($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.product_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.product_added_ok'), 'product.index');
    }

    /**
     *  show edit market place form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $product = $this->marketPlace->getMarketPlaceById($id);
        if (!$product) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.product_not_found'));
        }
        // get all countries
        $brands = (new AuthRepository())->getAllBrands();
        if (count($brands) == 0) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_products'));
        }
        return view('admin.rk-admin.market-place.products.edit', compact('product', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MarketPlaceRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(MarketPlaceRequest $request, $id)
    {

        $product = $this->marketPlace->getMarketPlaceById($id);
        if (!$product) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.product_not_found'));
        }

        // for checkBox
        if (!$request->has('is_active')) {
            $request['is_active'] = 0;
        }

        DB::beginTransaction();
        // update city data
        try {
            $this->marketPlace->updateMarketPlace($product, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.product_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.product_update_ok'), 'product.index');
    }

    /**
     * Remove Product in market place
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->marketPlace->marketPlace, $id);
    }
}
