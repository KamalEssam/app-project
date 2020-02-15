<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface InfluencersInterface
{

    /**
     * get list of all cities
     *
     * @return mixed
     */
    public function getAllInfluencers();

    /**
     *  get influencer by id
     *
     * @param $id
     * @return mixed
     */
    public function getInfluencerById($id);


    /**
     *  create new Country
     *
     * @param $request
     * @return mixed
     */
    public function createInfluencers($request);

    /**
     *  update City
     *
     * @param $influencer
     * @param $request
     * @return mixed
     */
    public function updateInfluencers($influencer, $request);
}
