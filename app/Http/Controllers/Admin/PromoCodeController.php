<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\PremiumPromoRepository;
use App\Http\Requests\PromoCodeRequest;
use DB;


class PromoCodeController extends WebController
{
    private $promoRepo;

    public function __construct(PremiumPromoRepository $premiumPromoRepository)
    {
        $this->promoRepo = $premiumPromoRepository;
    }


    /**
     *  get list of promo codes
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $promos = $this->promoRepo->getAllPromos();
        return view('admin.rk-admin.premium_promos.index', compact('promos'));
    }


    /**
     *  Store new visit by doctor
     *
     * @param PromoCodeRequest $promoCodeRequest
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(PromoCodeRequest $promoCodeRequest)
    {
        // add promo code
        DB::beginTransaction();
        try {
            $this->promoRepo->createPromo($promoCodeRequest);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.promo_code_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.promo_code_add_ok'), 'promo-code.index');
    }

    public function edit($id)
    {
        $promo_code = $this->promoRepo->getPromoById($id);
        if (!$promo_code) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_promos'));
        }

        return view('admin.rk-admin.premium_promos.edit', compact('promo_code'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PromoCodeRequest $request
     * @param  int $id
     * @return mixed
     * @throws \Exception
     */
    public function update(PromoCodeRequest $request, $id)
    {
        $promo = $this->promoRepo->getPromoById($id);
        if (!$promo) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_promos'));
        }

        DB::beginTransaction();
        // update promo code
        try {
            $this->promoRepo->updatePromo($promo, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.promo_code_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.promo_code_update_ok'), 'promo-code.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->promoRepo->promo, $id);

    }

}
