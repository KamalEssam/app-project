<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface AuthInterface
{
    /**
     *  get the normal users count
     * @return mixed
     */
    public static function getUsersCount();

    /**
     *  create new user in database
     *
     * @param $request
     * @param $active
     * @return mixed
     */
    public function createUSer($request, $active);

    /**
     *  get user providing the column and the value
     *  for example (unique_id,id,account_id,,)
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getUserByColumn($column, $value);

    /**
     * set user password ( used in case of change password or when user set password first time )
     *
     * @param $user
     * @param $password
     * @return mixed
     */
    public function setPassword($user, $password);

    /**
     *  get user by role id and account id
     *
     * @param $role
     * @param $account_id
     * @return mixed
     */
    public static function getUserByRoleAndAccountId($role, $account_id);

    /**
     *  get user by role_id and user_id
     *
     * @param $role
     * @param $user_id
     * @return mixed
     */
    public function getUserByRoleAndUserId($role, $user_id);

    /**
     *  get all mobiles of patients in the System
     *
     * @return mixed
     */
    public function getAllPatientsMobiles();

    /**
     *  get the name of the user using mobile
     *
     * @param $mobile
     * @return mixed
     */
    public function getPatientUsingMobile($mobile);

    /**
     *  update the last notification click to now
     *
     * @param $user
     * @return mixed
     */
    public function updateLastNotification($user);

    /**
     *  update user settings
     *
     * @param $user
     * @param $request
     * @return mixed
     */
    public function updateUser($user, $request);

    /**
     * @param $account_id
     * @param $role_id
     * @return mixed
     */
    public function getUsersUsingAccountAndRole($account_id, $role_id);
}