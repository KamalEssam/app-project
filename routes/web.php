<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// test send notifications
Route::get('/notification/test', [
    'uses' => 'Admin\NotificationController@pushNotificationTest'
]);

/**************************************** Auth *****************************************/
/* LOGIN ROUTES */
Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login')->name('post.login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

/*        Route to Android Store  */
Route::view('/android-store', 'admin.app.download_app');
/* ACCOUNT REGISTRATION */
Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register')->name('postRegister');

/************************************  sales register  *****************************************/
Route::get('/sales/register', 'Auth\RegisterController@showSalesRegistrationForm')->name('salesRegister');
Route::post('/sales/register', 'Auth\RegisterController@Salesregister')->name('postSalesRegister');

/* Create Password */
Route::get('/create-password/{id}', 'Admin\AdminController@getPasswordForm')->name('getPasswordForm');
Route::post('/store-password/{id}', 'Admin\AdminController@setPassword')->name('setPassword');

Route::get('/user-activate/{id}', 'Admin\AdminController@ActivateUser')->name('activateUser');    // Activate the user through message
Route::get('/resend-activate-link', 'Admin\AdminController@sendActivationLink')->name('sendActivationLink');    // resend activation link
/* RESET PASSWORD */
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('sendResetEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('resetPassword');
/* SUSPENDED ROUTES */
Route::get('/suspended', 'Auth\LoginController@suspended')->name('suspended');

Route::get('/newsletter-mail', 'Guest\HomeController@newsletterMail');
Route::get('/register-mail', 'Guest\HomeController@registerMail');

Route::get('/account/complete-steps', ['uses' => 'Admin\AccountController@CheckAccountCompletion', 'as' => 'account-completion', 'roles' => ['doctor']]);
Route::get('/poly-account/complete-steps', ['uses' => 'Admin\AccountController@CheckPolyAccountCompletion', 'as' => 'poly-account-completion', 'roles' => ['doctor']]);

Route::post('/account/send-notifications', ['uses' => 'Admin\AccountController@sendNotification', 'as' => 'notifications.send', 'roles' => ['rk-admin', 'rk-super', 'sales']]);

// get image with custom width
Route::get('/media/images/{name}/image.png', function ($name) {
    if (URL::to('/') == "http://localhost:8000") {
        return asset('/assets/images/offers/' . $name);
    }
    return Super::getImageWithWidth($name);
});
// doctor image cropping for Doctor
Route::post('image/upload', ['uses' => 'Admin\UserController@uploadUserCroppedImage', 'as' => 'image.upload', 'roles' => ['rk-admin', 'rk-super', 'doctor']]);

/*********************************************************** ADMIN ROUTES *******************************************************/
Route::group(['middleware' => ['auth', 'role', 'account_completion'], 'namespace' => 'Admin'], function () {

    Route::view('/', 'admin.dashboard', ['roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']])->name('admin');
    // admin dashboard reservations
    Route::get('/account/{id}/reservations', ['uses' => 'ReservationsController@getAccountReservations', 'as' => 'account.reservations', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('/reservations-statistics/', ['uses' => 'ReservationsController@getAllAccountsReservations', 'as' => 'account.reservations_all', 'roles' => ['rk-admin', 'rk-super']]);

    Route::get('/statistics/account/publish', 'StatisticsController@accountPublishStatistics');
    Route::get('/statistics/account/active', 'StatisticsController@accountActiveStatistics');
    Route::get('/statistics/account/premium', 'StatisticsController@accountPremiumStatistics');
    Route::get('/statistics/account/single', 'StatisticsController@accountSingleStatistics');


    Route::get('/statistics/account/registered', 'StatisticsController@registeredAccounts');
    Route::get('/statistics/account/doctor-reservations', 'StatisticsController@doctorReservations');


    // check if first time tour
    Route::post('/check-first-time', ['uses' => 'AdminController@checkFirstTimeTour', 'as' => 'check-first-time', 'roles' => ['doctor', 'assistant']]);

    Route::get('profile', ['uses' => 'UserController@index', 'as' => 'profile.index', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::get('profile/{id}/edit', ['uses' => 'UserController@edit', 'as' => 'profile.edit', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::patch('profile/{id}', ['uses' => 'UserController@update', 'as' => 'profile.update', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    /* Change Password ROUTES */
    Route::get('/change/password', ['uses' => 'AdminController@getChangePasswordForm', 'as' => 'get-change-password', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::post('/change/password', ['uses' => 'AdminController@postChangePassword', 'as' => 'post-change-password', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);

    /***************************************** General Settings **********************************/

    Route::view('general-settings/edit', 'admin.common.settings.edit', ['roles' => ['rk-admin', 'rk-super', 'assistant', 'doctor', 'sales']])->name('general-settings.edit');
    Route::patch('general-settings', ['uses' => 'GeneralSettingsController@update', 'as' => 'general-settings.update', 'roles' => ['rk-admin', 'rk-super', 'assistant', 'doctor', 'sales']]);
    /***************************************** RKAdmin **********************************/
    Route::get('rk-settings/{id}/edit', ['uses' => 'RkSettingsController@edit', 'as' => 'rk-settings.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('rk-settings/{id}', ['uses' => 'RkSettingsController@update', 'as' => 'rk-settings.update', 'roles' => ['rk-admin', 'rk-super']]);

    Route::get('accounts', ['uses' => 'AccountController@index', 'as' => 'accounts.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('accounts/{id}/access', ['uses' => 'AccountController@access', 'as' => 'accounts.access', 'roles' => ['rk-super']]);
    Route::get('accounts/create', ['uses' => 'AccountController@create', 'as' => 'accounts.create', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('accounts/show/{id}', ['uses' => 'AccountController@show', 'as' => 'accounts.show', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('accounts/{id}/edit', ['uses' => 'AccountController@edit', 'as' => 'accounts.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('accounts', ['uses' => 'AccountController@store', 'as' => 'accounts.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('accounts/{id}', ['uses' => 'AccountController@update', 'as' => 'accounts.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('accounts/{id}', ['uses' => 'AccountController@destroy', 'as' => 'accounts.destroy', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('accounts/{id}/publish/{status}', ['uses' => 'AccountController@publish', 'as' => 'accounts.publish', 'roles' => ['rk-super', 'rk-admin', 'doctor', 'sales']]);
    Route::get('accounts/{id}/activate', ['uses' => 'AccountController@activate', 'as' => 'accounts.activate', 'roles' => ['rk-admin', 'rk-super']]);

    //ajax route to get country cities
    Route::get('cities-select/{id}', ['uses' => 'SelectController@getFilteredCities', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('/load-row', ['uses' => 'AccountController@loadRow', 'roles' => ['rk-admin', 'rk-super']]);

    // Doctors Plans
    Route::get('plans', ['uses' => 'PlanController@index', 'as' => 'plans.index', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::get('plans/create', ['uses' => 'PlanController@create', 'as' => 'plans.create', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::get('plans/{id}/edit', ['uses' => 'PlanController@edit', 'as' => 'plans.edit', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::post('plans', ['uses' => 'PlanController@store', 'as' => 'plans.store', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::patch('plans/{id}', ['uses' => 'PlanController@update', 'as' => 'plans.update', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::delete('plans/{id}', ['uses' => 'PlanController@destroy', 'as' => 'plans.destroy', 'roles' => ['rk-admin', 'rk-super']]);

    // Patient Plans
    Route::get('patient-plans', ['uses' => 'PatientPlanController@index', 'as' => 'patient-plans.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('patient-plans/create', ['uses' => 'PatientPlanController@create', 'as' => 'patient-plans.create', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('patient-plans/{id}/edit', ['uses' => 'PatientPlanController@edit', 'as' => 'patient-plans.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('patient-plans', ['uses' => 'PatientPlanController@store', 'as' => 'patient-plans.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('patient-plans/{id}', ['uses' => 'PatientPlanController@update', 'as' => 'patient-plans.update', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::delete('patient-plans/{id}', ['uses' => 'PatientPlanController@destroy', 'as' => 'patient-plans.destroy', 'roles' => ['rk-admin', 'rk-super']]);

    // patients premium request
    Route::get('account/premium-requests', ['uses' => 'PremiumRequestController@index', 'as' => 'premium-requests.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('account/premium-requests/set-status', ['uses' => 'PremiumRequestController@changeStatus', 'as' => 'premium-requests.changeStatus', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('doctor/premium', ['uses' => 'PremiumRequestController@setDoctorPremium', 'as' => 'premium-requests.doctor-premium', 'roles' => ['doctor']]);

    Route::group(['prefix' => 'location'], function () {

        Route::get('cities', ['uses' => 'CityController@index', 'as' => 'cities.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('cities/create', ['uses' => 'CityController@create', 'as' => 'cities.create', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('cities/{id}/edit', ['uses' => 'CityController@edit', 'as' => 'cities.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('cities', ['uses' => 'CityController@store', 'as' => 'cities.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('cities/{id}', ['uses' => 'CityController@update', 'as' => 'cities.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('cities/{id}', ['uses' => 'CityController@destroy', 'as' => 'cities.destroy', 'roles' => ['rk-admin', 'rk-super']]);

        Route::get('provinces', ['uses' => 'ProvinceController@index', 'as' => 'provinces.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('provinces/create', ['uses' => 'ProvinceController@create', 'as' => 'provinces.create', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('provinces/{id}/edit', ['uses' => 'ProvinceController@edit', 'as' => 'provinces.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('provinces', ['uses' => 'ProvinceController@store', 'as' => 'provinces.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('provinces/{id}', ['uses' => 'ProvinceController@update', 'as' => 'provinces.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('provinces/{id}', ['uses' => 'ProvinceController@destroy', 'as' => 'provinces.destroy', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('provinces/list', ['uses' => 'ProvinceController@getProvincesByCityId', 'as' => 'provinces.list', 'roles' => ['doctor']]);

        Route::get('countries', ['uses' => 'CountryController@index', 'as' => 'countries.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::view('countries/create', 'admin.rk-admin.countries.create', ['roles' => ['rk-admin', 'rk-super']])->name('countries.create');
        Route::get('countries/{id}/edit', ['uses' => 'CountryController@edit', 'as' => 'countries.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('countries', ['uses' => 'CountryController@store', 'as' => 'countries.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('countries/{id}', ['uses' => 'CountryController@update', 'as' => 'countries.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('countries/{id}', ['uses' => 'CountryController@destroy', 'as' => 'countries.destroy', 'roles' => ['rk-admin', 'rk-super']]);
    });

    // speciality
    Route::get('specialities', ['uses' => 'SpecialityController@index', 'as' => 'specialities.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::view('specialities/create', 'admin.rk-admin.specialities.create', ['roles' => ['rk-admin', 'rk-super']])->name('specialities.create');
    Route::get('specialities/{id}/edit', ['uses' => 'SpecialityController@edit', 'as' => 'specialities.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('specialities', ['uses' => 'SpecialityController@store', 'as' => 'specialities.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('specialities/{id}', ['uses' => 'SpecialityController@update', 'as' => 'specialities.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('specialities/{id}', ['uses' => 'SpecialityController@destroy', 'as' => 'specialities.destroy', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('specialities/{id}/{status}', ['uses' => 'SpecialityController@featured', 'as' => 'specialities.featured', 'roles' => ['rk-admin', 'rk-super']]);

    // sponsored doctors
    Route::get('specialities/sponsored', ['uses' => 'SponsoredController@index', 'as' => 'sponsored.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('specialities/sponsored/{id}/create', ['uses' => 'SponsoredController@add', 'as' => 'sponsored.create', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('specialities/sponsored/store', ['uses' => 'SponsoredController@store', 'as' => 'sponsored.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('specialities/sponsored/show', ['uses' => 'SponsoredController@show', 'as' => 'sponsored.show', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('specialities/sponsored/{id}/doctors', ['uses' => 'SponsoredController@doctors', 'as' => 'sponsored.doctors', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('specialities/sponsored/{id}/remove', ['uses' => 'SponsoredController@remove', 'as' => 'sponsored.remove', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('specialities/sponsored/{id}/rank/{status}', ['uses' => 'SponsoredController@rank', 'as' => 'sponsored.rank', 'roles' => ['rk-admin', 'rk-super']]);

    // sub-speciality
    Route::view('specialities/{id}/sub-speciality/create', 'admin.rk-admin.specialities.sub-create', ['roles' => ['rk-admin', 'rk-super']])->name('specialities.sub-create');
    Route::post('sub-specialities', ['uses' => 'SubSpecialityController@store', 'as' => 'specialities.sub-store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('specialities/sub-speciality/all', ['uses' => 'SubSpecialityController@all', 'as' => 'specialities.sub-all', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('sub-specialities/{id}', ['uses' => 'SubSpecialityController@destroy', 'as' => 'specialities.sub-destroy', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('sub-specialities/{id}/edit', ['uses' => 'SubSpecialityController@edit', 'as' => 'specialities.sub-edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('sub-specialities/{id}', ['uses' => 'SubSpecialityController@update', 'as' => 'specialities.sub-update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('sub-specialities/list', ['uses' => 'SubSpecialityController@apiList', 'as' => 'specialities.sub-list', 'roles' => ['doctor']]);

//    Route::get('policies', ['uses' => 'PolicyController@index', 'as' => 'policies.index', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::view('policies/create', 'admin.rk-admin.policies.create', ['roles' => ['rk-admin', 'rk-super']])->name('policies.create');
    Route::get('policies/{id}/edit', ['uses' => 'PolicyController@edit', 'as' => 'policies.edit', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::post('policies', ['uses' => 'PolicyController@store', 'as' => 'policies.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('policies/{id}', ['uses' => 'PolicyController@update', 'as' => 'policies.update', 'roles' => ['rk-admin', 'rk-super']]);
//    Route::delete('policies/{id}', ['uses' => 'PolicyController@destroy', 'as' => 'policies.destroy', 'roles' => ['rk-admin', 'rk-super']]);

    Route::get('services', ['uses' => 'ServiceController@index', 'as' => 'services.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('services/create', ['uses' => 'ServiceController@create', 'as' => 'services.create', 'roles' => ['rk-admin', 'rk-super']]);
    Route::view('specialities/{id}/services/create', 'admin.rk-admin.specialities.service_add', ['roles' => ['rk-admin', 'rk-super']])->name('services.add');
    Route::get('services/{id}/edit', ['uses' => 'ServiceController@edit', 'as' => 'services.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('specialities/services/all', ['uses' => 'ServiceController@all', 'as' => 'services.all', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('services', ['uses' => 'ServiceController@store', 'as' => 'services.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('services/{id}', ['uses' => 'ServiceController@update', 'as' => 'services.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('services/{id}', ['uses' => 'ServiceController@destroy', 'as' => 'services.destroy', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('services/import', ['uses' => 'ServiceController@importServices', 'as' => 'services.imports', 'roles' => ['rk-admin', 'rk-super']]);

    // doctor services
    Route::get('doctor-services', ['uses' => 'DoctorServiceController@index', 'as' => 'doctor-services.index', 'roles' => ['doctor']]);
    Route::get('doctor-services/create', ['uses' => 'DoctorServiceController@create', 'as' => 'doctor-services.create', 'roles' => ['doctor']]);
    Route::get('doctor-services/{id}/edit', ['uses' => 'DoctorServiceController@edit', 'as' => 'doctor-services.edit', 'roles' => ['doctor']]);
    Route::post('doctor-services', ['uses' => 'DoctorServiceController@store', 'as' => 'doctor-services.store', 'roles' => ['doctor']]);
    Route::patch('doctor-services/{id}', ['uses' => 'DoctorServiceController@update', 'as' => 'doctor-services.update', 'roles' => ['doctor']]);
    Route::delete('doctor-services/{id}', ['uses' => 'DoctorServiceController@destroy', 'as' => 'doctor-services.destroy', 'roles' => ['doctor']]);

    /*****************************************Doctor**********************************/
    Route::get('assistants', ['uses' => 'AssistantController@index', 'as' => 'assistants.index', 'roles' => ['doctor']]);
    Route::view('assistants/create', 'admin.doctor.assistants.create', ['roles' => ['doctor']])->name('assistants.create');
    Route::post('assistants', ['uses' => 'AssistantController@store', 'as' => 'assistants.store', 'roles' => ['doctor']]);
    Route::get('assistants/{id}/edit', ['uses' => 'AssistantController@edit', 'as' => 'assistants.edit', 'roles' => ['doctor']]);
    Route::patch('assistants/{id}', ['uses' => 'AssistantController@update', 'as' => 'assistants.update', 'roles' => ['doctor']]);
    Route::delete('assistants/{id}', ['uses' => 'AssistantController@destroy', 'as' => 'assistants.destroy', 'roles' => ['doctor']]);
    Route::post('assistants/{id}/reset-password', ['uses' => 'AssistantController@resetPassword', 'as' => 'assistants.reset-password', 'roles' => ['doctor']]);

    Route::get('holiday/index', ['uses' => 'HolidayController@index', 'as' => 'holiday.index', 'roles' => ['assistant', 'doctor']]);
    Route::post('holiday', ['uses' => 'HolidayController@store', 'as' => 'holiday.store', 'roles' => ['assistant', 'doctor']]);
    Route::delete('holiday/{id}', ['uses' => 'HolidayController@destroy', 'as' => 'holiday.destroy', 'roles' => ['assistant', 'doctor']]);

    Route::get('clinics', ['uses' => 'ClinicController@index', 'as' => 'clinics.index', 'roles' => ['doctor']]);
    Route::view('clinics/create', 'admin.doctor.clinics.create', ['roles' => ['doctor']])->name('clinics.create');
    Route::post('clinics', ['uses' => 'ClinicController@store', 'as' => 'clinics.store', 'roles' => ['doctor']]);
    Route::get('clinics/{id}/edit', ['uses' => 'ClinicController@edit', 'as' => 'clinics.edit', 'roles' => ['assistant', 'doctor']]);
    Route::patch('clinics/{id}', ['uses' => 'ClinicController@update', 'as' => 'clinics.update', 'roles' => ['assistant', 'doctor']]);
    Route::delete('clinics/{id}', ['uses' => 'ClinicController@destroy', 'as' => 'clinics.destroy', 'roles' => ['doctor']]);


//    Route::get('queue/doctor', ['uses' => 'QueueController@doctorQueue', 'as' => 'queue.next', 'roles' => ['doctor']]);

    /*******************************************common****************************************/

    Route::get('visits', ['uses' => 'VisitController@index', 'as' => 'visits.index', 'roles' => ['doctor', 'assistant']]);
    Route::get('visits/create/{reservation_id}', ['uses' => 'VisitController@create', 'as' => 'visits.create', 'roles' => ['doctor', 'assistant']]);
    Route::get('visits/{id}/edit', ['uses' => 'VisitController@edit', 'as' => 'visits.edit', 'roles' => ['doctor', 'assistant']]);
    Route::post('visits/{reservation_id}', ['uses' => 'VisitController@store', 'as' => 'visits.store', 'roles' => ['doctor', 'assistant']]);
    Route::patch('visits/{id}', ['uses' => 'VisitController@update', 'as' => 'visits.update', 'roles' => ['doctor', 'assistant']]);
    Route::delete('visits/{id}', ['uses' => 'VisitController@destroy', 'as' => 'visits.destroy', 'roles' => ['doctor', 'assistant']]);
    Route::get('visits/{id}', ['uses' => 'VisitController@show', 'as' => 'visits.show', 'roles' => ['doctor', 'assistant']]);

    Route::get('visit/table-visits/{date?}/{name?}', ['uses' => 'VisitController@getFilteredVisit', 'roles' => ['doctor', 'assistant']]);

    Route::get('reservations/create', ['uses' => 'ReservationsController@create', 'as' => 'reservations.create', 'roles' => ['doctor', 'assistant']]);
    Route::post('reservations', ['uses' => 'ReservationsController@store', 'as' => 'reservations.store', 'roles' => ['doctor', 'assistant']]);
    Route::get('reservations/{id}/edit', ['uses' => 'ReservationsController@edit', 'as' => 'reservations.edit', 'roles' => ['doctor', 'assistant']]);
    Route::patch('reservations/{id}', ['uses' => 'ReservationsController@update', 'as' => 'reservations.update', 'roles' => ['doctor', 'assistant']]);

    Route::post('reservation/user_filter', ['uses' => 'ReservationsController@userResults', 'roles' => ['doctor', 'assistant']]);
    Route::post('reservation/refresh-user-results', ['uses' => 'ReservationsController@refreshResults', 'roles' => ['doctor', 'assistant']]);
    Route::post('reservation/time_reserved', ['uses' => 'ReservationsController@timeReserved', 'roles' => ['doctor', 'assistant']]);
    Route::put('reservation/set-status', ['uses' => 'ReservationsController@setStatus', 'roles' => ['doctor', 'assistant']]);
    Route::get('reservation/get-status/{id}', ['uses' => 'ReservationsController@getStatus', 'roles' => ['doctor', 'assistant']]);
    Route::get('reservations/{status?}', ['uses' => 'ReservationsController@index', 'as' => 'reservations.index', 'roles' => ['doctor', 'assistant']]);
    Route::get('reservation/table-reservations/{status?}/{date?}/{name?}', ['uses' => 'ReservationsController@getFilteredReservation', 'roles' => ['doctor', 'assistant']]);
    Route::post('reservation/check-date', ['uses' => 'ReservationsController@checkDate', 'as' => 'reservations.check-date', 'roles' => ['doctor', 'assistant']]);
    Route::get('reservation/{reservation_id}/standby', ['uses' => 'ReservationsController@setStandBy', 'as' => 'reservations.standBy', 'roles' => ['doctor', 'assistant']]);

    Route::get('reservation/{id}/details', ['uses' => 'ReservationsController@getReservationDetails', 'as' => 'reservations.details', 'roles' => ['rk-admin', 'rk-super', 'doctor', 'assistant']]);

    // set reservations paid
    Route::post('reservation/set-reservation-paid', ['uses' => 'ReservationsController@setCashReservationPaid', 'as' => 'reservations.set_reservation_paid', 'roles' => ['doctor', 'assistant']]);
    Route::post('reservation/check-transaction', ['uses' => 'ReservationsController@checkTransaction', 'as' => 'reservations.check_transaction', 'roles' => ['doctor', 'assistant']]);

    Route::get('patients', ['uses' => 'PatientController@index', 'as' => 'patients.index', 'roles' => ['doctor', 'assistant']]);

    // get list of patients for RK-Super Admin
    Route::get('patients/list', ['uses' => 'PatientController@allPatients', 'as' => 'patients.all', 'roles' => ['rk-admin', 'rk-super']]);

    /**********************************************assistant*********************************/
    Route::view('reservation/change-status', 'admin.common.reservations.change-status', ['roles' => ['doctor', 'assistant']]);

    Route::get('working-hours', ['uses' => 'WorkingHourController@index', 'as' => 'working-hours.index', 'roles' => ['doctor', 'assistant']]);
    Route::get('working-hours/create/{clinic_id?}', ['uses' => 'WorkingHourController@create', 'as' => 'working-hours.create', 'roles' => ['doctor', 'assistant']]);
    Route::post('working-hours', ['uses' => 'WorkingHourController@store', 'as' => 'working-hours.store', 'roles' => ['doctor', 'assistant']]);
    Route::delete('working-hours/reset/', ['uses' => 'WorkingHourController@reset', 'as' => 'working-hours.reset', 'roles' => ['doctor', 'assistant']]);
//    Route::get('working-hours/edit', ['uses' => 'WorkingHourController@edit', 'as' => 'working-hours.edit', 'roles' => ['doctor', 'assistant']]);
    Route::patch('working-hours/{id}', ['uses' => 'WorkingHourController@update', 'as' => 'working-hours.update', 'roles' => ['doctor', 'assistant']]);
    Route::post('working-hours/check-value/{clinic_id?}', ['uses' => 'WorkingHourController@checkValue', 'as' => 'working-hours.check-value', 'roles' => ['doctor', 'assistant']]);
    Route::post('working-hours/check-all', ['uses' => 'WorkingHourController@WorkingHoursCheck', 'as' => 'working-hours.check-all', 'roles' => ['doctor', 'assistant']]);
    Route::post('working-hours/get-deleted-reservations', ['uses' => 'WorkingHourController@getNumberOfReservationsOnWorkingHours', 'as' => 'working-hours.get-deleted-reservations', 'roles' => ['doctor', 'assistant']]);
    Route::post('working-hours/add-break', ['uses' => 'WorkingHourController@addBreak', 'as' => 'working-hours.add-break', 'roles' => ['doctor', 'assistant']]);
    Route::post('working-hours/delete-breaks', ['uses' => 'WorkingHourController@deleteBreaks', 'as' => 'working-hours.delete-break', 'roles' => ['doctor', 'assistant']]);


//    TODO :: queue commented until we decide to use it again
//    Route::get('queue', ['uses' => 'QueueController@index', 'as' => 'queue.index', 'roles' => ['assistant']]);
//    Route::get('queue/start', ['uses' => 'QueueController@startQueue', 'as' => 'queue.start', 'roles' => ['assistant']]);
//    Route::post('queue/change-status', ['uses' => 'QueueController@changeStatus', 'as' => 'queue.changeStatus', 'roles' => ['assistant']]);
//    Route::post('queue/next', ['uses' => 'QueueController@nextQueue', 'as' => 'queue.next', 'roles' => ['assistant']]);
//    Route::post('queue/push', ['uses' => 'QueueController@pushToQueue', 'as' => 'queue.push', 'roles' => ['assistant']]);
//    Route::post('queue/check-visit', ['uses' => 'QueueController@checkVisit', 'as' => 'queue.check-visit', 'roles' => ['assistant']]);

    Route::get('clinic/System', ['uses' => 'ClinicController@clinicSystem', 'as' => 'clinic.System', 'roles' => ['assistant']]);

    Route::get('clinic-settings/{id}', ['uses' => 'ClinicSettingsController@edit', 'as' => 'clinic-settings.edit', 'roles' => ['doctor', 'assistant']]);
    Route::patch('clinic-settings/{id}', ['uses' => 'ClinicSettingsController@update', 'as' => 'clinic-settings.update', 'roles' => ['doctor', 'assistant']]);

    Route::post('patients/store', ['uses' => 'PatientController@storeInReservation', 'as' => 'patients.store', 'roles' => ['assistant']]);
    Route::get('patients/mobile-validation', ['uses' => 'PatientController@validateMobile', 'as' => 'patients.email.validate', 'roles' => ['doctor', 'assistant']]);


    /********************************** manage sales agents **************************************/
    Route::get('sales/agents', ['uses' => 'SalesController@index', 'as' => 'sales.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::view('sales/create', 'admin.rk-admin.sales.create', ['roles' => ['rk-admin', 'rk-super']])->name('sales.create');
    Route::post('sales', ['uses' => 'SalesController@store', 'as' => 'sales.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('sales/{id}/edit', ['uses' => 'SalesController@edit', 'as' => 'sales.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('sales/{id}', ['uses' => 'SalesController@update', 'as' => 'sales.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('sales/{id}', ['uses' => 'SalesController@destroy', 'as' => 'sales.destroy', 'roles' => ['rk-admin', 'rk-super']]);

    Route::get('sales/logs', ['uses' => 'SalesController@logs', 'as' => 'sales.logs', 'roles' => ['rk-admin', 'rk-super']]);

    /****************************************Notification*****************************************/
    Route::post('notification/set-token', ['uses' => 'NotificationController@setToken', 'as' => 'notifications.setToken', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::get('notifications-list', ['uses' => 'NotificationController@index', 'as' => 'notifications.index', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::get('notifications-list-load-more', ['uses' => 'NotificationController@loadMore', 'as' => 'notifications.load.more', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::post('notifications/last-click', ['uses' => 'NotificationController@lastNotificationClick', 'as' => 'notifications.last-click', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::get('notifications/counter', ['uses' => 'NotificationController@counterBox', 'as' => 'notifications.counter', 'roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']]);
    Route::view('notifications/list', 'includes.admin.notifications-list', ['roles' => ['doctor', 'assistant', 'rk-admin', 'rk-super', 'sales', 'brand']])->name('notifications.list');


    Route::group(['prefix' => 'marketing'], function () {

        // insurance companies
        Route::get('insurance_company', ['uses' => 'InsuranceCompanyController@index', 'as' => 'insurance_company.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::view('insurance_company/create', 'admin.rk-admin.insurance_companies.create', ['roles' => ['rk-admin', 'rk-super']])->name('insurance_company.create');
        Route::get('insurance_company/{id}/edit', ['uses' => 'InsuranceCompanyController@edit', 'as' => 'insurance_company.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('insurance_company', ['uses' => 'InsuranceCompanyController@store', 'as' => 'insurance_company.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('insurance_company/{id}', ['uses' => 'InsuranceCompanyController@update', 'as' => 'insurance_company.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('insurance_company/{id}', ['uses' => 'InsuranceCompanyController@destroy', 'as' => 'insurance_company.destroy', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('insurance_company/import', ['uses' => 'InsuranceCompanyController@importCompanies', 'as' => 'insurance_company.imports', 'roles' => ['rk-admin', 'rk-super']]);

        // list of influencers on the application
        Route::get('influencers', ['uses' => 'InfluencersController@index', 'as' => 'influencers.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::view('influencers/create', 'admin.rk-admin.influencers.create', ['roles' => ['rk-admin', 'rk-super']])->name('influencers.create');
        Route::post('influencers', ['uses' => 'InfluencersController@store', 'as' => 'influencers.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('influencers/{id}/edit', ['uses' => 'InfluencersController@edit', 'as' => 'influencers.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('influencers/{id}', ['uses' => 'InfluencersController@update', 'as' => 'influencers.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('influencers/{id}', ['uses' => 'InfluencersController@destroy', 'as' => 'influencers.destroy', 'roles' => ['rk-admin', 'rk-super']]);

        // promo codes
        Route::get('promo-code', ['uses' => 'PromoCodeController@index', 'as' => 'promo-code.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::view('promo-code/create', 'admin.rk-admin.premium_promos.create', ['roles' => ['rk-admin', 'rk-super']])->name('promo-code.create');
        Route::post('promo-code', ['uses' => 'PromoCodeController@store', 'as' => 'promo-code.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('promo-code/{id}/edit', ['uses' => 'PromoCodeController@edit', 'as' => 'promo-code.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('promo-code/{id}', ['uses' => 'PromoCodeController@update', 'as' => 'promo-code.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('promo-code/{id}', ['uses' => 'PromoCodeController@destroy', 'as' => 'promo-code.destroy', 'roles' => ['rk-admin', 'rk-super']]);

        Route::get('subscribers', ['uses' => 'SubscriptionController@index', 'as' => 'subscriptions.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::view('subscriptions/create', 'admin.rk-admin.subscriptions.create', ['roles' => ['rk-admin', 'rk-super']])->name('subscriptions.create');
        Route::post('subscriptions', ['uses' => 'SubscriptionController@store', 'as' => 'subscriptions.store', 'roles' => ['rk-admin', 'rk-super']]);

    });


    // Offer Categories
    Route::get('offer_categories', ['uses' => 'OffersCategoriesController@index', 'as' => 'offer_categories.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::view('offer_categories/create', 'admin.rk-admin.offer_categories.create', ['roles' => ['rk-admin', 'rk-super']])->name('offer_categories.create');
    Route::post('offer_categories', ['uses' => 'OffersCategoriesController@store', 'as' => 'offer_categories.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('offer_categories/{id}/edit', ['uses' => 'OffersCategoriesController@edit', 'as' => 'offer_categories.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('offer_categories/{id}', ['uses' => 'OffersCategoriesController@update', 'as' => 'offer_categories.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('offer_categories/{id}', ['uses' => 'OffersCategoriesController@destroy', 'as' => 'offer_categories.destroy', 'roles' => ['rk-admin', 'rk-super']]);

    // Offer
    Route::get('offers', ['uses' => 'OffersController@index', 'as' => 'offers.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::view('offers/create', 'admin.rk-admin.offers.create', ['roles' => ['rk-admin', 'rk-super']])->name('offers.create');
    Route::post('offers', ['uses' => 'OffersController@store', 'as' => 'offers.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('offers/{id}/edit', ['uses' => 'OffersController@edit', 'as' => 'offers.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('offers/{id}', ['uses' => 'OffersController@show', 'as' => 'offers.show', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('offers/{id}', ['uses' => 'OffersController@update', 'as' => 'offers.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('offers/{id}', ['uses' => 'OffersController@destroy', 'as' => 'offers.destroy', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('offers/get-doctors', ['uses' => 'OffersController@getDoctors', 'roles' => ['rk-admin', 'rk-super']]);

    //  Ads
    Route::get('offer/ads', ['uses' => 'AdsController@index', 'as' => 'ads.index', 'roles' => ['rk-admin', 'rk-super']]);
    Route::view('offer/ads/create', 'admin.rk-admin.ads.create', ['roles' => ['rk-admin', 'rk-super']])->name('ads.create');
    Route::post('offer/ads', ['uses' => 'AdsController@store', 'as' => 'ads.store', 'roles' => ['rk-admin', 'rk-super']]);
    Route::get('offer/ads/{id}/edit', ['uses' => 'AdsController@edit', 'as' => 'ads.edit', 'roles' => ['rk-admin', 'rk-super']]);
    Route::patch('offer/ads/{id}', ['uses' => 'AdsController@update', 'as' => 'ads.update', 'roles' => ['rk-admin', 'rk-super']]);
    Route::delete('offer/ads/{id}', ['uses' => 'AdsController@destroy', 'as' => 'ads.destroy', 'roles' => ['rk-admin', 'rk-super']]);


    //     SALES ROLE ROUTES
    Route::group(['prefix' => 'sale'], function () {
        Route::get('accounts', ['uses' => 'SalesController@accounts', 'as' => 'sale.accounts', 'roles' => ['sales']]);
        Route::get('account-steps', ['uses' => 'SalesController@accountSteps', 'as' => 'sale.accounts_steps', 'roles' => ['sales', 'rk-admin', 'rk-super']]);


        // Sale Leads Crud
        Route::get('leads', ['uses' => 'SalesLeadsController@index', 'as' => 'leads.index', 'roles' => ['sales']]);
        Route::get('leads/export', ['uses' => 'SalesLeadsController@exportLeads', 'as' => 'leads.exports', 'roles' => ['sales']]);
        Route::post('leads/import', ['uses' => 'SalesLeadsController@importLeads', 'as' => 'leads.imports', 'roles' => ['sales']]);
        Route::view('leads/create', 'admin.sale.leads.create', ['roles' => ['sales']])->name('leads.create');
        Route::post('leads', ['uses' => 'SalesLeadsController@store', 'as' => 'leads.store', 'roles' => ['sales']]);
        Route::get('leads/{id}/edit', ['uses' => 'SalesLeadsController@edit', 'as' => 'leads.edit', 'roles' => ['assistant', 'sales']]);
        Route::patch('leads/{id}', ['uses' => 'SalesLeadsController@update', 'as' => 'leads.update', 'roles' => ['assistant', 'sales']]);
        Route::delete('leads/{id}', ['uses' => 'SalesLeadsController@destroy', 'as' => 'leads.destroy', 'roles' => ['sales']]);
    });

    //     Brand ROLE ROUTES
    Route::group(['prefix' => 'market-place'], function () {

        Route::get('brands', ['uses' => 'BrandController@index', 'as' => 'brands.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::view('brands/create', 'admin.rk-admin.market-place.brands.create', ['roles' => ['rk-admin', 'rk-super']])->name('brands.create');
        Route::post('brands', ['uses' => 'BrandController@store', 'as' => 'brands.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('brands/{id}', ['uses' => 'BrandController@destroy', 'as' => 'brands.destroy', 'roles' => ['rk-admin', 'rk-super']]);

        Route::get('product', ['uses' => 'MarketPlaceController@index', 'as' => 'product.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('product/create', ['uses' => 'MarketPlaceController@create', 'as' => 'product.create', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('product', ['uses' => 'MarketPlaceController@store', 'as' => 'product.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('product/{id}/edit', ['uses' => 'MarketPlaceController@edit', 'as' => 'product.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('product/{id}', ['uses' => 'MarketPlaceController@update', 'as' => 'product.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('product/{id}', ['uses' => 'MarketPlaceController@destroy', 'as' => 'product.destroy', 'roles' => ['rk-admin', 'rk-super']]);

        Route::get('category', ['uses' => 'MarketPlaceCategoryController@index', 'as' => 'category.index', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('category/create', ['uses' => 'MarketPlaceCategoryController@create', 'as' => 'category.create', 'roles' => ['rk-admin', 'rk-super']]);
        Route::post('category', ['uses' => 'MarketPlaceCategoryController@store', 'as' => 'category.store', 'roles' => ['rk-admin', 'rk-super']]);
        Route::get('category/{id}/edit', ['uses' => 'MarketPlaceCategoryController@edit', 'as' => 'category.edit', 'roles' => ['rk-admin', 'rk-super']]);
        Route::patch('category/{id}', ['uses' => 'MarketPlaceCategoryController@update', 'as' => 'category.update', 'roles' => ['rk-admin', 'rk-super']]);
        Route::delete('category/{id}', ['uses' => 'MarketPlaceCategoryController@destroy', 'as' => 'category.destroy', 'roles' => ['rk-admin', 'rk-super']]);

    });

    // user gallery
    Route::get('gallery', ['uses' => 'GalleryController@index', 'as' => 'gallery.index', 'roles' => ['doctor']]);
    Route::view('gallery/create', 'admin.doctor.gallery.create', ['roles' => ['doctor']])->name('gallery.create');
    Route::post('gallery', ['uses' => 'GalleryController@store', 'as' => 'gallery.store', 'roles' => ['doctor']]);
    Route::delete('gallery/{id}', ['uses' => 'GalleryController@destroy', 'as' => 'gallery.destroy', 'roles' => ['doctor']]);


    Route::get('financial', ['uses' => 'DoctorFinancialController@index', 'as' => 'financial.report', 'roles' => ['doctor']]);

    Route::get('reports/all', ['uses' => 'ReportsController@index', 'as' => 'reports', 'roles' => ['rk-admin', 'rk-super']]);
    Route::post('/report/change-status', ['uses' => 'ReportsController@changeStatus', 'as' => 'reports.change-status', 'roles' => ['rk-admin', 'rk-super']]);
});
