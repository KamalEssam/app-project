<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface SalesInterface
{

    /**
     * get list of all added sales accounts
     *
     * @return mixed
     */
    public function getSalesAddedAccounts();

    /**
     * get first city to set it default to doctors
     * @param $user_id
     * @param $filter
     * @return mixed
     */
    public function getCurrentSalesAddedAccounts($user_id,$filter);


    /**
     *  get count of all added accounts
     *
     * @return mixed
     */
    public function getSalesAccountCount();
}
