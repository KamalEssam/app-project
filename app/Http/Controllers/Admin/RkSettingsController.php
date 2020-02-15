<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\SettingRepository;
use App\Http\Requests\SettingsRequest;
use DB;

class RkSettingsController extends WebController
{

    private $settingRepository;

    /**
     * SettingController constructor.
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * edit settings for rk
     * @return mixed
     */
    public function edit()
    {
        $setting = $this->settingRepository::getFirstSetting();
        if (!$setting) {
            return $this->messageAndRedirect(self::STATUS_ERR, 'Failed go to settings');
        }
        return $this->messageAndReturnView(self::STATUS_NONE, 'edit setting page', 'admin.rk-admin.settings.edit', ['setting' => $setting]);
    }

    /**
     *  update the settings
     *
     * @param SettingsRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function update(SettingsRequest $request)
    {

        $doctors = $request['doctors'];
        $patients = $request['patients'];

        unset($request['doctors'], $request['patients']);

        if (!isset($request->debug_mode)) {
            $request['debug_mode'] = 0;
        }

        $setting = $this->settingRepository->updateSettings($this->settingRepository::getFirstSetting(), $request);

        if (!$setting) {
            return $this->messageAndRedirect(self::STATUS_ERR, 'Failed go to settings');
        }

        $this->updateTestData($doctors, 'doctor');
        $this->updateTestData($patients, 'patient');

        return $this->messageAndRedirect(self::STATUS_OK, 'setting updated successfully', 'rk-settings.edit', ['setting' => $setting]);
    }

    /**
     *  update list of debug data
     *
     * @param $data
     * @param string $type
     */
    private function updateTestData($data, $type)
    {
        if ($type === 'doctor') {
            $currentUsers = DB::table('test_data')->where('type', $type)->pluck('user_id')->toArray();
        } elseif ($type === 'patient') {
            $currentUsers = DB::table('test_data')->where('type', $type)->pluck('user_id')->toArray();
        }

        if (!is_array($currentUsers)) {
            $currentUsers = [];
        }

        if (!is_array($data)) {
            $data = [];
        }

        $new = array_diff($data, $currentUsers);  // new users to be inserted
        $removed = array_diff($currentUsers, $data); // deleted users
        if (count($removed) > 0) {
            // remove the doctors
            DB::table('test_data')->whereIn('user_id', $removed)->delete();
        }
        if (count($new) > 0) {
            $doctor_data = [];
            foreach ($new as $new_user) {
                $doctor_data[] = [
                    'type' => $type,
                    'user_id' => $new_user,
                    'created_at' => now()
                ];
            }
            DB::table('test_data')->insert($doctor_data);
        }

    }
}
