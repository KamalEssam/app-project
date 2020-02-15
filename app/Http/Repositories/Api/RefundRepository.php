<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;

use App\Http\Repositories\Web\ParentRepository;
use App\Models\Refund;

class RefundRepository extends ParentRepository
{
    protected $refund;

    public function __construct()
    {
        $this->refund = new Refund();
    }

    /**
     *  add refund request
     *
     * @param $user_id
     * @param $reservation_id
     * @param $condition
     * @return bool
     */
    function addRefundRequest($user_id, $reservation_id, $condition)
    {
        try {
            return $this->refund->create([
                'user_id' => $user_id,
                'reservation_id' => $reservation_id,
                'condition' => $condition,
            ]);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function checkForRefundRequest($user_id, $reservation_id)
    {
        try {
            return $this->refund->where('reservation_id', $reservation_id)->where('user_id', $user_id)->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

}
