<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface OfferInterface
{

    /**
     * get list of all categories
     *
     * @return mixed
     */
    public function getAllOffers();


    /**
     * get offer by id
     *
     * @param $id
     * @return mixed
     */
    public function getOfferById($id);

    /**
     *  create new offer
     *
     * @param $request
     * @return mixed
     */
    public function createOffer($request);

    /**
     *  update offer
     *
     * @param $offer
     * @param $request
     * @return mixed
     */
    public function updateOffer($offer, $request);

    /**
     *  get row providing the column and the value
     *  for example (id,,)
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumn($column, $value);

    /**
     *  get offers for the api
     *
     * @param $is_featured
     * @param $request
     * @return mixed
     */
    public function ApiGetOffers($is_featured, $request);

    /**
     *  increase
     *
     * @param $offer_id
     * @return mixed
     */
    public function ApiIncreaseOffersViews($offer_id);
}
