<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\OfferCategoryRepository;
use App\Http\Requests\OfferCategoryRequest;
use DB;

class OffersCategoriesController extends WebController
{
    private $offerCategoryRepo;

    public function __construct(OfferCategoryRepository $offerCategoryRepository)
    {
        $this->offerCategoryRepo = $offerCategoryRepository;
    }

    /**
     *  show list of all cities
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $offerCategories = $this->offerCategoryRepo->getAllCategories();
        return view('admin.rk-admin.offer_categories.index', compact('offerCategories'));
    }

    /**
     * Store new offer category in database
     *
     * @param OfferCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(OfferCategoryRequest $request)
    {
        // add offer category
        DB::beginTransaction();
        try {
            $this->offerCategoryRepo->createOfferCategory($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.offer_category_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.offer_category_add_ok'), 'offer_categories.index');
    }

    /**
     *  show edit offer category
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $offerCategory = $this->offerCategoryRepo->getOfferCategoryById($id);
        if (!$offerCategory) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_offer_categories'));
        }
        return view('admin.rk-admin.offer_categories.edit', compact('offerCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OfferCategoryRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(OfferCategoryRequest $request, $id)
    {
        $offerCategory = $this->offerCategoryRepo->getOfferCategoryById($id);
        if (!$offerCategory) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_offer_categories'));
        }

        DB::beginTransaction();
        // update city data
        try {
            $this->offerCategoryRepo->updateOfferCategory($offerCategory, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.offer_category_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.offer_category_update_ok'), 'offer_categories.index');
    }

    /**
     * Remove offer category
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->offerCategoryRepo->offerCategory, $id);
    }
}
