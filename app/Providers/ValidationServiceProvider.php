<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // parent interface and repository

        ///        API
        $apiInterfacesAndRepositories = [
            'ValidationInterface' => 'ValidationRepository',
            'AuthValidationInterface' => 'AuthValidationRepository',
            'DoctorValidationInterface' => 'DoctorValidationRepository',
            'ClinicValidationInterface' => 'ClinicValidationRepository',
            'WorkingHourValidationInterface' => 'WorkingHourValidationRepository',
            'ReservationValidationInterface' => 'ReservationValidationRepository',
        ];

        foreach ($apiInterfacesAndRepositories as $key => $value) {

            $this->app->bind(
                "App\Http\InterFaces\Api\\$key",
                "App\Http\Repositories\Api\\$value"
            );
        }

    }
}
