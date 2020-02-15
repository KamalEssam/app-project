<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface AdInterface
{

    /**
     *  get Ad by id
     *
     * @param $id
     * @return mixed
     */
    public function getAdById($id);

    /**
     *  create new Ad
     *
     * @param $request
     * @return mixed
     */
    public function createAd($request);

    /**
     *  update ad
     *
     * @param $ad
     * @param $request
     * @return mixed
     */
    public function updateAd($ad, $request);

    /**
     *  get all ads by id
     *
     * @return mixed
     */
    public function getAds();

    /**
     *  get slides with latest offers
     *
     * @return mixed
     */
    public function getSlidesWithOffers();

}
