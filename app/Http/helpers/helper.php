<?php

/*
 *  get the  value of debug mode
 *
 * */
if (!function_exists('debug_mode')) {
    function debug_mode()
    {
        $general_settings = App\Models\Setting::first();  // check debug mode
        return !$general_settings->debug_mode ?? false;   // get debug mode;
    }
}

/*
 *  get the  value of debug mode
 *
 * */
if (!function_exists('get_test_users')) {
    /**
     * @param $type (doctor, patient)
     * @return array|\Illuminate\Support\Collection
     */
    function get_test_users($type)
    {
        $users = DB::table('test_data')->where('type', $type)->pluck('user_id');
        return $users ?? array();
    }
}
