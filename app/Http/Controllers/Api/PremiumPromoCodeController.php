<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\PromoCodeRepository;
use App\Http\Repositories\Web\PremiumPromoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class PremiumPromoCodeController extends ApiController
{
    public function __construct(Request $request)
    {
        $this->setLang($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPromoCodeToUser(Request $request)
    {
        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        $validator = Validator::make($request->all(), [
            'promo_code' => 'required|exists:premium_promo_codes,code'
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }

        $promo = (new PremiumPromoRepository())->getPromoByCode($request->promo_code);

        $diff = now()->diffInDays(Carbon::parse($promo->expiry_date), false);

        if ($diff < 0) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.code_expired'));
        }
        // check if user used this promo code before
        if ((new PremiumPromoRepository())->checkIfUserUsedPromoCode($user->id, $promo->id)) {
            return self::jsonResponse(false, self::CODE_OK, trans('lang.code_expired'), [], $promo);
        }

        try {
            (new AuthRepository())->updateColumn($user, 'premium_code_id', $promo->id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.whoops'));
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.code_add_ok'), [], $promo);
    }

    public function getTotalFeeAfterRedeemCode(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'promo-code' => 'required',
            'total' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), $validator->errors());
        }

        $user = auth()->guard('api')->user();
        // if not unauthorized
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }

        $total = $request['total'];
        $promoCode = (new PromoCodeRepository())->getCodeValueAfterValidation($user->id, $request['promo-code']);
        if ($promoCode) {
            $response = new \stdClass();
            $lang = app()->getLocale();
            if ($promoCode->discount_type == 1) {
                $total -= ($total * ($promoCode->discount / 100));
                $response->discount = $lang == 'en' ? $promoCode->discount . '% discount' : '%خصم ' . $promoCode->discount;
            } else {
                $total -= $promoCode->discount;
                $response->discount = $lang == 'en' ? $promoCode->discount . ' EGP discount' : 'خصم ' . $promoCode->discount . ' جنيه';
            }

            $response->total = $total;

            return self::jsonResponse(false, self::CODE_OK, trans('lang.success'), '', $response);
        }
        return self::jsonResponse(false, self::CODE_FAILED, trans('lang.code_invalid'));
    }
}
