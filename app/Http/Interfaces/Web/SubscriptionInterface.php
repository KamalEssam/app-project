<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface SubscriptionInterface
{
    /**
     *  get the normal users count
     * @return mixed
     */
    public static function getSubscribersCount();

    /**
     *  get all subscribers
     * @return mixed
     */
    public function getAllSubscribers();

}