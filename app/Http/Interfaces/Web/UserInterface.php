<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface UserInterface
{

    /**'
     * get profile data by auth user id
     * @param $user_id
     * @return mixed
     */
    public function getUserById($user_id);

    /**
     * get doctor settings
     * @param $account_id
     * @return mixed
     */
    public function getDoctorDetailsByAccountId($account_id);

    /**
     * get doctor account
     * @param $account_id
     * @return mixed
     */
    public function getAccountById($account_id);

    /**
     * get doctor plan
     * @param $plan_id
     * @return mixed
     */
    public function getPlanById($plan_id);

    /**
     * get doctor speciality
     * @param $speciality_id
     * @return mixed
     */
    public function getSpecialityById($speciality_id);

    /**
     * get doctor data plus his profile
     * @param $profile
     * @param $method
     * @return mixed
     */
    public function getDoctorAccountData($profile, $method);

    /**
     * update profile data
     * @param $profile
     * @param $request
     * @return mixed
     */
    public function updateProfileData($profile, $request);

    /**
     * when update profile update doctor details
     * @param $profile
     * @param $request
     * @return mixed
     */
    public function updateDoctorDetails($profile, $request);

    /**
     * set profile image
     * @param $profile
     * @param $request
     * @return mixed
     */
    public function setUserImage($profile, $request);
}