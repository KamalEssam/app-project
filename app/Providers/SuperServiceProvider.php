<?php

namespace App\Providers;
use App\Repositories\SuperRepo;

use Illuminate\Support\ServiceProvider;

class SuperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('super', function (){
            return new SuperRepo;
        });
    }
}
