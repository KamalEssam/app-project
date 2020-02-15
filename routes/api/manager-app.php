<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// sign up
Route::post('/sign-up', [
    'uses' => 'AuthController@doctorSignUp',
    'as' => 'sign-up'
]);

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('/patients', [
        'uses' => 'PatientController@getPatientsRelatedToThisDoctor',
    ]);
    // start the queue in case of Assistant app or get the Current Reservation in case of Doctor
    Route::post('/queue/start', [
        'uses' => 'ManagerReservationController@startQueue',
    ]);
    // get the next reservation in queue
    Route::post('/queue/next', [
        'uses' => 'ManagerReservationController@nextQueue',
    ]);

    Route::post('/reservations/standBy', [
        'uses' => 'ManagerReservationController@setReservationAsStandBy',
    ]);

    // get the current reservation
    Route::post('/reservations/current', [
        'uses' => 'ManagerReservationController@getCurrentReservation',
    ]);

    // get upcoming reservations
    Route::post('/reservations/upcoming', [
        'uses' => 'ManagerReservationController@getUpcomingReservations',
    ]);

    // get upcoming reservations
    Route::post('/patient/add', [
        'uses' => 'PatientController@addPatient',
    ]);

    // get upcoming reservations
    Route::post('/profile/update', [
        'uses' => 'AuthController@completeDoctorData',
    ]);

    // get upcoming reservations
    Route::post('/set/services', [
        'uses' => 'AuthController@setServices',
    ]);

    Route::post('/doctor/premium/change', [
        'uses' => 'AuthController@changePremium'
    ]);

    // validate transaction for user
    Route::post('/transaction/validate', [
        'uses' => 'ReservationController@setOnlineReservationPaid'
    ]);
});

// doctor and assistant login
Route::post('/login', [
    'uses' => 'AuthController@doctorLogin',
]);

// doctor and assistant login
Route::post('/services/all', [
    'uses' => 'AuthController@getAllServices',
]);
