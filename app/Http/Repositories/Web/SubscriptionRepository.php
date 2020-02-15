<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\SubscriptionInterface;
use App\Models\Subscription;

class SubscriptionRepository extends ParentRepository implements SubscriptionInterface
{
    protected $subscription;

    public function __construct()
    {
        $this->subscription = new Subscription();
    }

    /**
     *  get the normal users count
     * @return mixed
     */
    public static function getSubscribersCount()
    {
        try {
            return Subscription::count();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
    /**
     *  get all subscribers
     * @return mixed
     */
    public function getAllSubscribers()
    {
        try {
            return Subscription::all();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}