<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\SettingInterface;
use App\Models\Setting;
use DB;

class SettingRepository extends ParentRepository implements SettingInterface
{
    protected $setting;

    public function __construct()
    {
        $this->setting = new Setting();
    }

    /**
     *  get the first setting
     *
     * @return mixed
     */
    public static function getFirstSetting()
    {
        try {
            return Setting::first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update the settings
     *
     * @param $setting
     * @param $request
     * @return bool|mixed
     * @throws \Exception
     */
    public function updateSettings($setting,$request)
    {
        DB::beginTransaction();
        try {
            $setting->update($request->all());
        } catch (\Exception $e) {
            DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
        DB::commit();
        return $setting;
    }
}