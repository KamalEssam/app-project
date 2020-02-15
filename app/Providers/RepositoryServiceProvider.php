<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        // here we bind interfaces and repositories to our project


        ///        API
        $apiInterfacesAndRepositories = [
            'AttachmentInterface' => 'AttachmentRepository',
            'AuthInterface' => 'AuthRepository',
            'ClinicInterface' => 'ClinicRepository',
            'DoctorInterface' => 'DoctorRepository',
            'NotificationInterface' => 'NotificationRepository',
            'ProfileInterface' => 'ProfileRepository',
            'ReservationInterface' => 'ReservationRepository',
            'SettingInterface' => 'SettingRepository',
            'TokenInterface' => 'TokenRepository',
            'WorkingHourInterface' => 'WorkingHourRepository',
        ];

        foreach ($apiInterfacesAndRepositories as $key => $value) {
            $this->app->bind(
                "App\Http\InterFaces\Api\$key",
                "App\Http\Repositories\Api\$value"
            );
        }


        ///        WEB
        $webInterfacesAndRepositories = [
            'AccountInterface' => 'AccountRepository',
            'AssistantInterface' => 'AssistantRepository',
            'AuthInterface' => 'AuthRepository',
            'CityInterface' => 'CityRepository',
            'ProvinceInterface' => 'ProvinceRepository',
            'ClinicQueueInterface' => 'ClinicQueueRepository',
            'ClinicInterface' => 'ClinicRepository',
            'CountryInterface' => 'CountryRepository',
            'DoctorDetailInterface' => 'DoctorDetailRepository',
            'PlanInterface' => 'PlanRepository',
            'PatientPlanInterface' => 'PatientPlanRepository',
            'PolicyInterface' => 'PolicyRepository',
            'ReservationInterface' => 'ReservationRepository',
            'ServiceInterface' => 'ServiceRepository',
            'SettingInterface' => 'SettingRepository',
            'SpecialityInterface' => 'SpecialityRepository',
            'SubscriptionInterface' => 'SubscriptionRepository',
            'TokenInterface' => 'TokenRepository',
            'VisitInterface' => 'VisitRepository',
            'WorkingHourInterface' => 'WorkingHourRepository',
            'StandByInterface' => 'StandByRepository',
            'NotificationInterface' => 'NotificationRepository',
            'HolidayInterface' => 'HolidayRepository',
            'InfluencersInterface' => 'InfluencersRepository',
            'InsuranceCompanyInterface' => 'InsuranceCompanyRepository',
        ];

        foreach ($webInterfacesAndRepositories as $key => $value) {
            $this->app->bind(
                "App\Http\InterFaces\Web\$key",
                "App\Http\Repositories\Web\$value"
            );
        }
    }
}
