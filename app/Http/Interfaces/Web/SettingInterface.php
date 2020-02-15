<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface SettingInterface
{
    /**
     *  get the accounts count
     *
     * @return mixed
     */
    public static function getFirstSetting();

    /**
     *  update the settings
     *
     * @param $settings
     * @param $request
     * @return mixed
     */
    public function updateSettings($settings,$request);
}