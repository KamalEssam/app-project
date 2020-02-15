<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface AccountInterface
{
    /**
     *  get the accounts count
     *
     * @return mixed
     */
    public static function getAccountsCount();

    /**
     *  get all the accounts ordered by  ( created_by )
     *
     * @return mixed
     */
    public function getAllAccountsOrdered($type);

    /**
     *  create new account
     *
     * @param $request
     * @return mixed
     */
    public function createAccount($request);

    /**
     *  get account by id
     *
     * @param $id
     * @return mixed
     */
    public static function getAccountById($id);

    /**
     * update account data
     *
     * @param $account
     * @param $plan_id
     * @param $days
     * @param $price_of_day
     * @param $auth_id
     * @return mixed
     */
    public function updateAccount($account, $plan_id, $days, $price_of_day, $auth_id);

}
