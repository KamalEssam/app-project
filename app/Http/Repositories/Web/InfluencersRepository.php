<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\InfluencersInterface;
use App\Models\Influencers;

class InfluencersRepository extends ParentRepository implements InfluencersInterface
{
    public $influencer;

    public function __construct()
    {
        $this->influencer = new Influencers();
    }

    /**
     * get list of all cities
     *
     * @return mixed
     */
    public function getAllInfluencers()
    {
        try {
            return $this->influencer->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get influencer by id
     *
     * @param $id
     * @return mixed
     */
    public function getInfluencerById($id)
    {
        try {
            return $this->influencer->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Country
     *
     * @param $request
     * @return mixed
     */
    public function createInfluencers($request)
    {
        return $this->influencer->create($request->all());
    }

    /**
     *  update City
     *
     * @param $influencer
     * @param $request
     * @return mixed
     */
    public function updateInfluencers($influencer, $request)
    {
        return $influencer->update($request->all());
    }

    /**
     *  get array of influencers
     *
     * @return bool
     */
    public function getArrayOfInfluencers()
    {
        try {
            return $this->influencer->pluck( app()->getLocale() . '_name as name' ,'id');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return null;
        }
    }

}
