<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\PremiumRequestInterface;
use App\Models\PremiumRequest;

class PremiumRequestRepository extends ParentRepository implements PremiumRequestInterface
{
    public $premium;

    public function __construct()
    {
        $this->premium = new PremiumRequest();
    }

    /**
     * get list of requests with status
     *
     * @param $status
     * @return mixed
     */
    public function getAllRequestsByStatus($status)
    {
        try {
            return $this->premium->where('approval', $status)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new premium request
     *
     * @param $request
     * @return mixed
     */
    public function createPremiumRequest($request)
    {
        try {
            $premium = $this->premium->create($request);
            return $premium;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  set status to premium request
     *
     * @param $premium
     * @param $status
     * @return mixed
     */
    public function SetRequestStatus($premium, $status)
    {
        try {
            $premium->update([
                'approval' => $status
            ]);

            return $premium;

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get premium request by id
     *
     * @param $request_id
     * @return mixed
     */
    public function getPremiumRequestById($request_id)
    {
        try {
            $premium = $this->premium->find($request_id);
            return $premium;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $user_id
     * @param int $status
     * @return bool
     */
    public function getRequestByUserIdAndStatus($user_id, $status = -1)
    {
        try {
            $premium = $this->premium
                ->where('approval', $status)
                ->where('user_id', $user_id)
                ->first();
            return $premium;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getUserRequestsByUserId($user_id)
    {
        try {
            $requests = $this->premium
                ->where('user_id', $user_id)
                ->where('approval', -1)
                ->orderBy('created_at', 'desc')
                ->get();
            return $requests;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}