<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface MarketPlaceInterface
{

    /**
     * get list of all market-places
     *
     * @return mixed
     */
    public function getAllMarketPlaces();

    /**
     *  get market-place by id
     *
     * @param $id
     * @return mixed
     */
    public function getMarketPlaceById($id);


    /**
     *  create new market-place
     *
     * @param $request
     * @return mixed
     */
    public function createMarketPlace($request);

    /**
     *  update market-place
     *
     * @param $influencer
     * @param $request
     * @return mixed
     */
    public function updateMarketPlace($marketPlace, $request);

    /**
     *  get products for api
     *
     * @param $user // in case of user is logged in
     * @param $request
     * @return mixed
     */
    public function getApiProducts($user, $request);

    /**
     * @param $product_id
     * @return mixed
     */
    public function getProductDetails($product_id);
}
