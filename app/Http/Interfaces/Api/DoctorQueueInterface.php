<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Api;


interface DoctorQueueInterface
{
    /**
     *  assistant start queue
     *
     * @param $auth_user
     * @return mixed
     */
    public function startQueue($auth_user);
}