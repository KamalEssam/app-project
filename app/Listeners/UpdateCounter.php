<?php

namespace App\Listeners;

use App\Events\UserGenerated;
use App\Http\Repositories\Api\SettingRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCounter
{
    private $settingRepository;

    /**
     * Create the event listener.
     *
     * @param SettingRepository $setting
     */
    public function __construct(SettingRepository $setting)
    {
        $this->settingRepository = $setting;
    }

    /**
     * Handle the event.
     *
     * @param  UserGenerated $event
     * @return bool
     */
    public function handle(UserGenerated $event)
    {
        switch ($event->type) {
            case 1 :
                // doctor
                $setting = $this->settingRepository->getFirstSetting();
                $account_counter = $setting->account_counter;
                $account_counter += 1;
                $setting->account_counter = $account_counter;
                $setting->update();
                break;
            case 2 :
                // assistant
                $setting = $this->settingRepository->getFirstSetting();
                $assistant_counter = $setting->assistant_counter;
                $assistant_counter += 1;
                $setting->assistant_counter = $assistant_counter;
                $setting->update();
                break;
            case 3 :
                // user
                $setting = $this->settingRepository->getFirstSetting();
                $user_counter = $setting->user_counter;
                $user_counter += 1;
                $setting->user_counter = $user_counter;
                $setting->update();
                break;
            default :
                // default action
                return false;

        }
    }
}
