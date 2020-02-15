<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\MarketPlaceInterface;
use App\Models\MarketPlace;
use App\Models\Redeems;
use DB;
use Illuminate\Database\Eloquent\Collection;

class MarketPlaceRepository extends ParentRepository implements MarketPlaceInterface
{
    public $marketPlace;

    public function __construct()
    {
        $this->marketPlace = new MarketPlace();
    }

    /**
     * get list of all market-places
     *
     * @return mixed
     */
    public function getAllMarketPlaces()
    {
        try {
            return $this->marketPlace->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get market-place by id
     *
     * @param $id
     * @return mixed
     */
    public function getMarketPlaceById($id)
    {
        try {
            return $this->marketPlace->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new market-place
     *
     * @param $request
     * @return mixed
     */
    public function createMarketPlace($request)
    {
        return $this->marketPlace->create($request->all());
    }

    /**
     *  update market-place
     *
     * @param $marketPlace
     * @param $request
     * @return mixed
     */
    public function updateMarketPlace($marketPlace, $request)
    {
        return $marketPlace->update($request->all());
    }

    /**
     *  get products for api
     *  in case of user has already redeemed products dont show them
     *
     * @param $user // in case of user is logged in
     * @param $request
     * @return mixed
     */
    public function getApiProducts($user, $request)
    {

        $offset = (isset($request->offset) && !empty($request->offset)) ? $request->offset : 0;
        $limit = (isset($request->limit) && !empty($request->limit)) ? $request->limit : 10;

        $category_id = $request['category_id'];

        try {
            return $this->marketPlace::join('users', 'users.id', 'market_places.brand_id')
                ->active()
                ->notexpired()
                ->where(function ($query) use ($user, $category_id) {
                    if ($user) {
                        // dont get redeemed products
                        $query->whereNotIn('market_places.id', Redeems::where('user_id', $user->id)->select('product_id as id')->get());
                    }

                    if ($category_id) {
                        $query->where('market_place_category_id', $category_id);
                    }

                })
                ->select(
                    'market_places.id',
                    'market_places.market_place_category_id as category_id',
                    'market_places.image',
                    'market_places.price',
                    app()->getLocale() . '_title as title',
                    app()->getLocale() . '_desc as desc',
                    'users.name as brand_name',
                    'users.unique_id',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as brand_image')
                )
                ->offset($offset)
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  get products for api
     *
     * @param $user // in case of user is logged in
     * @return mixed
     */
    public function getApiVouchers($user)
    {
        try {
            return $this->marketPlace::join('users', 'users.id', 'market_places.brand_id')
                ->join('user_market_places', 'user_market_places.product_id', 'market_places.id')
                ->where('user_market_places.user_id', $user->id)
                ->where('user_market_places.is_used', 0)
                ->active()
                ->notexpired()
                ->select(
                    'market_places.id',
                    'market_places.image',
                    'market_places.price',
                    'market_places.market_place_category_id as category_id',
                    app()->getLocale() . '_title as title',
                    app()->getLocale() . '_desc as desc',
                    'users.name as brand_name',
                    'users.unique_id',
                    'user_market_places.expiry_date',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as brand_image')
                )
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     * @param  $product_id
     * @return mixed
     */
    public function getProductDetails($product_id)
    {
        try {
            // here active, notexpired  are both local scopes in MarketPlace model
            return $this->marketPlace::join('users', 'users.id', 'market_places.brand_id')
                ->active()
                ->notexpired()
                ->where('market_places.id', $product_id)
                ->select(
                    'market_places.id',
                    'market_places.image',
                    'market_places.price',
                    app()->getLocale() . '_title as title',
                    app()->getLocale() . '_desc as desc',
                    'users.name as brand_name',
                    'users.unique_id',
                    DB::raw('IF(users.image = "default.png",CONCAT("' . asset('assets/images/') . '","/", users.image), CONCAT("' . asset('assets/images/profiles/') . '/",users.unique_id,"/", users.image)) as brand_image')
                )
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new \stdClass();
        }
    }
}
