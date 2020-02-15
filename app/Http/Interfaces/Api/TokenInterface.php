<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Api;


interface TokenInterface
{
    /**
     *  get token by user and serial
     *
     * @param $request
     * @return mixed
     */
    public function getTokenByUserAndSerial($request);


    /**
     *
     *  get token by user and serial
     *
     * @param $request
     * @return mixed
     */
    public function setToken($request);

    /**
     *  remove token by serial
     *
     * @param $serial
     * @return mixed
     */
    public function removeToken($serial);
}