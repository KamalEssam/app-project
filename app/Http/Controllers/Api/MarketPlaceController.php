<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Validation\MarketPlaceValidationRepository;
use App\Http\Repositories\Web\MarketPlaceCategoryRepository;
use App\Http\Repositories\Web\MarketPlaceRepository;
use App\Http\Traits\UserTrait;
use App\Models\Redeems;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class MarketPlaceController extends ApiController
{

    public $marketPlaceRepository;
    public $marketPlaceValidationRepository;
    use UserTrait;

    /**
     *  marketplaceController constructor.
     * @param Request $request
     * @param MarketPlaceRepository $marketPlace
     * @param MarketPlaceValidationRepository $marketPlaceValidationRepository
     */
    public function __construct(Request $request, MarketPlaceRepository $marketPlace, MarketPlaceValidationRepository $marketPlaceValidationRepository)
    {
        $this->marketPlaceRepository = $marketPlace;
        $this->marketPlaceValidationRepository = $marketPlaceValidationRepository;
        $this->setLang($request);
    }


    /**
     *  get list of products and vouchers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        $user = auth()->guard('api')->user();

        $products = $this->marketPlaceRepository->getApiProducts($user, $request);

        if (!$products) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_products'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.total_products'), [], $products);
    }

    public function getCategories(Request $request)
    {
        $categories = (new MarketPlaceCategoryRepository())->getApiCategories();

        if (!$categories) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_categories'));
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.total_categories'), [], $categories);
    }

    /**
     *  get list of products and vouchers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVouchers(Request $request)
    {
        $user = auth()->guard('api')->user();
        if ($user) {
            $vouchers = $this->marketPlaceRepository->getApiVouchers($user);
        } else {
            $vouchers = new Collection();
        }
        if (!$vouchers) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.no_products'));
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.total_products'), [], $vouchers);
    }


    /**
     *  redeem product
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function redeemProduct(Request $request)
    {
        $user = auth()->guard('api')->user();

        // check for user existence
        if ($user == null) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'));
        }
        // Check if patient
        if (!self::checkIfPatient($user)) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.invalid-role'));
        }

        // validate fields
        if (!$this->marketPlaceValidationRepository->productDetailsValidation($request)) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $this->marketPlaceValidationRepository->getFirstError(), $this->marketPlaceValidationRepository->getErrors(), []);
        }

        // first get the product, then compare between user points and product points
        $product = $this->marketPlaceRepository->getMarketPlaceById($request['product_id']);

        if (!$product) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.product_not_found'));
        }

        // in case user dont have enough points
        if ($user->cash_back < $product->price) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.dont-have-enough-cash'));
        }

        // check if offer is redeemed before by this user or not
        $user_redeemed = Redeems::where('user_id', $user->id)->where('product_id', $product->id)->first();
        if ($user_redeemed) {
            return self::jsonResponse(false, self::CODE_RECORD_EXISTS, trans('lang.product_redeemed_once'));
        }

        DB::beginTransaction();
        try {
            $data[$product->id] =
                array(
                    'is_used' => 0,
                    'expiry_date' => now()->addDays($product->redeem_expiry_days)
                );

            // create record in database for this user and this product
            $user->redeem()->attach($data);
        } catch (\Exception $ex) {
            self::logErr($ex);
            DB::rollBack();
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.whoops'));
        }

        // sub the price of product from user points
        (new AuthRepository())->updateColumn($user, 'cash_back', $user->cash_back - $product->price);

        // decrease the number of uses of this product
        $product->decrement('max_redeems', 1);

        DB::commit();
        // return the ok status
        return self::jsonResponse(true, self::CODE_OK, trans('lang.redeem_was_successfully'));
    }
}
