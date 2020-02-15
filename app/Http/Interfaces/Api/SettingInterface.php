<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Api;


interface SettingInterface
{
    /**
     *  get first setting
     *
     * @return mixed
     */
    public function getFirstSetting();

    /**
     * get about us data
     *
     * @return mixed
     */
    public function getAboutUS();

    /**
     *  get contact us data
     *
     * @return mixed
     */
    public function getContactUs();

}