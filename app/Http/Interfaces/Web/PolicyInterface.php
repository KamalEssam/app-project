<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface PolicyInterface
{

    /**
     * get list of all Policies
     *
     * @return mixed
     */
    public function getAllPolicies();

    /**
     * get Policy by id
     *
     * @param $id
     * @return mixed
     */
    public function getPolicyById($id);


    /**
     *  create new Policy
     *
     * @param $request
     * @return mixed
     */
    public function createPolicy($request);

    /**
     *  update Policy
     *
     * @param $plan
     * @param $request
     * @return mixed
     */
    public function updatePolicy($plan, $request);

    /**
     * @return mixed
     */
    public function getPoliciesForSite($type);
}