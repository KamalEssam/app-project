<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface PlanInterface
{
    /**
     *  get the accounts count
     *
     * @return mixed
     */
    public static function getPlansCount();

    /**
     * get list of all plans
     *
     * @return mixed
     */
    public function getAllPlans();


    /**
     * get plan by id
     *
     * @param $id
     * @return mixed
     */
    public function getPlanById($id);

    /**
     *  create new Plan
     *
     * @param $request
     * @return mixed
     */
    public function createPlan($request);

    /**
     *  update Plan
     *
     * @param $plan
     * @param $request
     * @return mixed
     */
    public function updatePlan($plan,$request);
}