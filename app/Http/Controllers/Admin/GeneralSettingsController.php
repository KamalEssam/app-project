<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Requests\GeneralSettingsRequest;
use DB;

class GeneralSettingsController extends WebController
{
    /**
     *  update the settings
     *
     * @param GeneralSettingsRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function update(GeneralSettingsRequest $request)
    {

        if (!isset($request->is_notification)) {
            $request['is_notification'] = 0;
        }

        // check for language
        DB::beginTransaction();
        try {
            (new AuthRepository())->updateUser(auth()->user(), $request);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_OK, trans('lang.settings_update_err'));
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.settings_update_ok'));
    }
}