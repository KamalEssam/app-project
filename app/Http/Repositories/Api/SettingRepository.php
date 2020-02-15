<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;


use App\Http\Interfaces\Api\SettingInterface;
use App\Models\Setting;

class SettingRepository implements SettingInterface
{
    protected $user;

    public function __construct()
    {
        $this->setting = new Setting();
    }

    /**
     *  get first setting
     *
     * @return mixed
     */
    public function getFirstSetting()
    {
        try {
            return $this->setting->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * get about us data
     *
     * @return mixed
     */
    public function getAboutUS()
    {
        try {
            return $this->setting->first()[app()->getLocale() . '_about_us'];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     *  get contact us data
     *
     * @return mixed
     */
    public function getContactUs()
    {
        try {
            return $this->setting->select('facebook', 'twitter', 'instagram', 'youtube', 'website', 'mobile', 'email')->first();
        } catch (\Exception $e) {
            return false;
        }
    }
}
