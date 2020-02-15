<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\PlanInterface;
use App\Models\PatientPlan;

class PatientPlanRepository extends ParentRepository implements PlanInterface
{
    public $plan;

    public function __construct()
    {
        $this->plan = new PatientPlan();
    }

    /**
     *  get the accounts count
     *
     * @return mixed
     */
    public static function getPlansCount()
    {
        try {
            return PatientPlan::count();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get list of all plans
     *
     * @return mixed
     */
    public function getAllPlans()
    {
        try {
            return $this->plan->all();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get list of all plans
     *
     * @return mixed
     */
    public function getApiPlans()
    {
        try {
            return $this->plan->select('id', app()->getLocale() . '_name as name', app()->getLocale() . '_desc as desc', 'price', 'months', 'points')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * get plan by id
     *
     * @param $id
     * @return mixed
     */
    public function getPlanById($id)
    {
        try {
            return $this->plan->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Plan
     *
     * @param $request
     * @return mixed
     */
    public function createPlan($request)
    {
        try {
            return $this->plan->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update Plan
     *
     * @param $plan
     * @param $request
     * @return mixed
     */
    public function updatePlan($plan, $request)
    {
        try {
            return $plan->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
