<?php

namespace App\Providers;

use Carbon\Carbon;
use DB;
use Illuminate\Support\ServiceProvider;
use Log;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        \Schema::defaultStringLength(191);
        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            return substr($value, 0, 2) == '01';
        });
        Carbon::setLocale(app()->getLocale());

        // this part prints all used queries in the log file
//        DB::listen(function ($query) {
//            Log::info(' query => ' . $query->sql);
//        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
