<?php

namespace App\Http\Interfaces\Api;

interface AuthInterface
{

    /**
     *  update created by
     * @param $record
     * @param $created_by
     * @return mixed
     */
    public function setCreatedBy($record, $created_by);
    /**
     *  check user existence
     * @param $mobile
     * @return mixed
     */
    public function getUserWithMobile($mobile);

    /**
     * @param $email
     * @return mixed
     */
    public function getUserWithEmail($email);

    /**
     * @param $column
     * @param $social_id
     * @return mixed
     */
    public function getUserWithSocialId($column, $social_id);

    /**
     * @param $user
     * @param $password
     * @return mixed
     */
    public function setPassword($user, $password);

    /**
     * create new user
     * @param $request
     * @return mixed
     */
    public function createUser($request);

    /**
     * update user data after create
     *
     * @param $userCounter
     * @param $user
     * @return mixed
     */
    public function updateAfterCreate($userCounter, $user);

    /**
     *  create token for user
     *
     * @param $user
     * @return mixed
     */
    public function createToken($user);

    /**
     *  log user in
     *
     * @param $request
     * @return mixed
     */
    public function attemptLogin($request);

    /**
     * @param $user
     * @return mixed
     */
    public function getUserData($user);

    /**
     * @param $user
     * @return mixed
     */
    public function activateUser($user);

    /**
     *  update password for user
     *
     * @param $user
     * @param $new_password
     * @return mixed
     */
    public function updatePassword($user, $new_password);

    /**
     *  get user by account
     * @param $account_id
     * @param $role
     * @return mixed
     */
    public function getUserByAccount($account_id, $role);

    /**
     *  get user
     * @param $user_id
     * @return mixed
     */
    public function getUserById($user_id);

    /**
     * get account
     * @param $account_id
     * @return mixed
     */
    public function getAccountById($account_id);

    /**
     * @param $user
     * @param $column
     * @param $value
     * @return mixed
     */
    public function updateColumn($user, $column, $value);

    /**
     * @param $user
     * @param $new_password
     * @return mixed
     */
    public function updateUserPassword($user, $new_password);

    /**
     * get user by mobile number
     * @param $mobile
     * @return mixed
     */
    public function getUserByMobile($mobile);

    /**
     * update new user
     * @param $user
     * @param $request
     * @return mixed
     */
    public function updateUser($user ,$request);


    /**
     * @param $unique_id
     * @return mixed
     */
    public function getUserByUniqueId($unique_id);
}
