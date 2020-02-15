<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\PolicyInterface;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Collection;

class PolicyRepository extends ParentRepository implements PolicyInterface
{
    public $policy;

    public function __construct()
    {
        $this->policy = new Policy();
    }

    /**
     * get list of all policies
     *
     * @return mixed
     */
    public function getAllPolicies()
    {
        try {
            return $this->policy->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     * get Policy by id
     *
     * @param $id
     * @return mixed
     */
    public function getPolicyById($id)
    {
        try {
            return Policy::find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Policy
     *
     * @param $request
     * @return mixed
     */
    public function createPolicy($request)
    {
        try {
            return $this->policy->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update Policy
     *
     * @param $policy
     * @param $request
     * @return mixed
     */
    public function updatePolicy($policy, $request)
    {
        try {
            return $policy->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param int $type
     * @return bool|mixed
     */
    public function getPoliciesForSite($type = 1)
    {
        try {
            return $this->policy
                ->where('type', $type)
                ->select(
                    app()->getLocale() . '_name as name',
                    app()->getLocale() . '_condition as condition',
                    'type'
                )->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}
