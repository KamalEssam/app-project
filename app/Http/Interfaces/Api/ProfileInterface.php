<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;

interface ProfileInterface
{
    /**
     * get user data with custom data
     * @param $user
     * @return mixed
     */
    public function getProfileCustomData($user);

    /**
     * get user data
     * @param $user
     * @return mixed
     */
    public function getProfile($user);

    /**
     * edit user data
     * @param $user
     * @param $request
     * @return mixed
     */
    public function editProfile($user, $request);

    /**
     * set profile image
     * @param $user
     * @param $image
     * @return mixed
     */
    public function setImage($user, $image);

}