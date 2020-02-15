<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\MarketPlaceCategoryRepository;
use App\Http\Requests\MarketPlaceCategoryRequest;
use DB;

class MarketPlaceCategoryController extends WebController
{
    private $marketPlaceCategory;

    public function __construct(MarketPlaceCategoryRepository $marketPlaceCategory)
    {
        $this->marketPlaceCategory = $marketPlaceCategory;
    }

    /**
     *  show list of all market-places categories products
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = $this->marketPlaceCategory->getAllCategories();
        return view('admin.rk-admin.market-place.categories.index', compact('categories'));
    }

    /**
     *  Show the form for creating a market place category.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // get all countries
        return view('admin.rk-admin.market-place.categories.create');
    }

    /**
     * Store new market-place in database
     *
     * @param MarketPlaceCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MarketPlaceCategoryRequest $request)
    {
        if (!$request->has('is_active')) {
            $request['is_active'] = 0;
        }
        // add market place category
        DB::beginTransaction();
        try {
            $this->marketPlaceCategory->createCategory($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.category_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.category_added_ok'), 'category.index');
    }

    /**
     *  show edit market place form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $category = $this->marketPlaceCategory->getCategoryById($id);
        if (!$category) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.category_not_found'));
        }
        return view('admin.rk-admin.market-place.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MarketPlaceCategoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MarketPlaceCategoryRequest $request, $id)
    {
        $product = $this->marketPlaceCategory->getCategoryById($id);
        if (!$product) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.category_not_found'));
        }

        if (!$request->has('is_active')) {
            $request['is_active'] = 0;
        }

        DB::beginTransaction();
        // update city data
        try {
            $this->marketPlaceCategory->updateCategory($product, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.category_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.category_update_ok'), 'category.index');
    }

    /**
     * Remove category in market place
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->marketPlaceCategory->marketPlaceCategory, $id);
    }
}
