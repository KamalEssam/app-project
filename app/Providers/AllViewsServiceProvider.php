<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;

class AllViewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // global variables in all views with the user roles
        View::share('role_doctor', '1');
        View::share('role_assistant', '2');
        View::share('role_user', '3');
        View::share('role_rk_admin', '4');
        View::share('role_rk_super_admin', '5');
        View::share('role_rk_sales', '6');
        View::share('role_brand', '7');

        // TODO change that variable when change domain
        // used in mails
        View::share('current_domain', 'https://admin-seena.com/');

        // we can use $auth in any view as auth()->user, u can't get data by this way $auth->id not working
        //View::share('auth', auth()->user());

        View::composer('*', function ($view) {
            $view->with('auth', auth()->user());
            $view->with('arabic_regex', "^[\u0621-\u064A0-9_ !-.?&()]+$");     // regex for arabic language
            $view->with('english_regex', '^[a-zA-Z0-9_ !-.?&()]*$'); // regex for english language
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
