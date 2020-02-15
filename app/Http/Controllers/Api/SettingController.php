<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\SettingRepository;
use Illuminate\Http\Request;

class SettingController extends ApiController
{
    private $settings;

    public function __construct(SettingRepository $settingRepository, Request $request)
    {
        $this->settings = $settingRepository;
        $this->setLang($request);
    }

    /**
     *  about us
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aboutUs()
    {

        $about_us = $this->settings->getAboutUS();

        if (!$about_us) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.about_us'), new \stdClass(), new \stdClass());
        }

        return self::jsonResponse(true, self::CODE_OK, trans('lang.about_us'), new \stdClass(), $about_us);
    }

    public function contactUs()
    {
        $contact_us = $this->settings->getContactUs();

        $contact_us->facebook_key = substr($contact_us->facebook, strripos($contact_us->facebook, "/", 0) + 1);
        $contact_us->twitter_key = substr($contact_us->twitter, strripos($contact_us->twitter, "/", 0) + 1);
        $contact_us->youtube_key = substr($contact_us->youtube, strripos($contact_us->youtube, "/", 0) + 1);
        $contact_us->instagram_key = substr($contact_us->instagram, strripos($contact_us->instagram, "/", 0) + 1);

        if (!$contact_us) {
            return self::jsonResponse(false, self::CODE_FAILED, trans('lang.about_us'), new \stdClass(), new \stdClass());
        }
        return self::jsonResponse(true, self::CODE_OK, trans('lang.about_us'), new \stdClass(), $contact_us);
    }

}
