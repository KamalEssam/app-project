<?php

namespace App\Http\Traits;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use App\Models\User;

trait UserTrait
{
    /**
     * get user by id
     * @param $user_id
     * @return mixed
     */
    public static function getUserById($user_id){
              return  User::where('id', $user_id)->first();

    }

    /**
     * check if this user is doctor of assistant
     * @param $user
     * @return bool
     */
    public static function checkIfDoctorOrAssistant($user){
        // Check if user doctor or assistant
        return ($user->role_id == ApiController::ROLE_DOCTOR || $user->role_id == ApiController::ROLE_ASSISTANT);
    }

    /**
     * check if this user is patient
     * @param $user
     * @return bool
     */
    public static function checkIfPatient($user)
    {
        // Check if user patient
        return $user->role_id == ApiController::ROLE_USER;
    }

    /**
     * check if this user is doctor
     * @param $user_id
     * @return bool
     */
    public static function checkIfDoctor($user_id)
    {
        // Check user exists
        $user = User::where('id', $user_id)->where('role_id', 1)->first();
        if (!$user) {
            return false;
        }
        return $user;
    }
    /**
     * check if this user is assistant
     * @param $user_id
     * @return bool
     */
    public static function checkIfAssistant($user_id)
    {
        // Check user exists
        $user = User::where('id', $user_id)
            ->where('role_id', 2)
            ->first();
        if (!$user) {
            return false;
        }
        return $user;
    }

    /**
     * get account by id
     * @param $account_id
     * @return mixed
     */
    public static function getAccountById($account_id){
        return Account::where('id', $account_id)->first();
    }
 /*   public static function addPathToImage($user, $image){
        return asset('assets/images/accounts/'  . $user->unique_id . '/' . $image);
    }*/
}