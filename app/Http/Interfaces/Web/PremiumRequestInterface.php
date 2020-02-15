<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface PremiumRequestInterface
{

    /**
     * get list of requests with status
     *
     * @param $status
     * @return mixed
     */
    public function getAllRequestsByStatus($status);


    /**
     *  create new premium request
     *
     * @param $request
     * @return mixed
     */
    public function createPremiumRequest($request);

    /**
     *  set status to premium request
     *
     * @param $premium
     * @param $status
     * @return mixed
     */
    public function SetRequestStatus($premium, $status);

    /**
     *  get premium request by id
     *
     * @param $request_id
     * @return mixed
     */
    public function getPremiumRequestById($request_id);
}