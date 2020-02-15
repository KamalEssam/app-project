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

Route::group(['middleware' => 'auth:api'], function () {
    // activate account
    Route::post('/user/activate-account', [
        'uses' => 'AuthController@activateAccount'
    ]);
    // change user password
    Route::post('/user/change-password', [
        'uses' => 'AuthController@changePassword',
        'as' => 'change-password'
    ]);

    // set Token
    Route::post('/set-token', [
        'uses' => 'TokenController@setToken',
    ]);

    // get user profile
    Route::post('/profile', [
        'uses' => 'ProfileController@getProfile',
    ]);

    // get user image
    Route::post('/profile/set-image', [
        'uses' => 'ProfileController@setProfileImage',
    ]);

    // edit profile
    Route::post('/profile/edit', [
        'uses' => 'ProfileController@editProfile',
    ]);

    // Log Out
    Route::post('/logout', [
        'uses' => 'AuthController@logout',
    ]);

    // get upcoming Reservation details
    Route::post('/reservation/upcoming/details', [
        'uses' => 'ReservationController@upcomingReservationDetails',
        'as' => 'reservation-upcoming-details'
    ]);

    // get past Reservation details
    Route::post('/reservation/past/details', [
        'uses' => 'ReservationController@pastReservationDetails',
        'as' => 'reservation-upcoming-details'
    ]);

    Route::post('/notification/set-status', [
        'uses' => 'AuthController@userNotificationStatus',
    ]);

    Route::post('/premium-request', [
        'uses' => 'PatientController@sendPremiumRequest',
    ]);

    Route::post('/premium-request/cancel', [
        'uses' => 'PatientController@cancelPremiumRequest',
    ]);

    // get upcoming reservation
    Route::post('/reservation/upcoming', [
        'uses' => 'ReservationController@getUpcomingReservation',
    ]);

    // get past reservation
    Route::post('/reservation/past', [
        'uses' => 'ReservationController@getPastReservation',
    ]);

    // add and remove doctor to list
    Route::post('/doctor/add-remove-list', [
        'uses' => 'DoctorController@addAndRemoveToFavouriteList',
    ]);
    // add reservation
    Route::post('/reservation/add', [
        'uses' => 'ReservationController@addReservation',
    ]);
    // edit reservation
    Route::post('/reservation/reschedule', [
        'uses' => 'ReservationController@rescheduleReservation',
    ]);

    // redeem product
    Route::post('/product/redeem', [
        'uses' => 'MarketPlaceController@redeemProduct',
    ]);


    // market place
    Route::post('/marketplace/vouchers', [
        'uses' => 'MarketPlaceController@getVouchers'
    ]);


    // send request for cashback
    Route::post('/cashback/request', [
        'uses' => 'CashBackController@requestCashBack'
    ]);

    // Redeem promo-code
    Route::post('/reservation/promo-code/redeem', [
        'uses' => 'PremiumPromoCodeController@getTotalFeeAfterRedeemCode'
    ]);

    Route::post('/report/add', [
        'uses' => 'ReportController@addReport'
    ]);

    Route::post('/refund/request', [
        'uses' => 'RefundController@addRefundRequest'
    ]);

});

Route::post('/doctor/recommend', [
    'uses' => 'DoctorController@recommendDoctorAccount',
]);

// resend sms message
Route::post('/sms/resend', [
    'uses' => 'AuthController@resendVerificationCode',
]);

// get my doctors list
Route::post('/doctors/my-list', [
    'uses' => 'DoctorController@getMyDoctorsList',
]);
Route::post('/reservation/set-status', [
    'uses' => 'ReservationController@setReservationStatus',
]);
// get Reservation Fees
Route::post('/reservation/fees', [
    'uses' => 'ReservationController@getReservationFees',
]);

//  splash service
Route::post('/splash', [
    'uses' => 'DoctorController@getSplashService',
]);
// login
Route::post('/user/login', [
    'uses' => 'AuthController@login',
]);
Route::post('/user/social-login', [
    'uses' => 'AuthController@socialLogin',
]);
// verify pin
Route::post('/user/pin/verify', [
    'uses' => 'AuthController@verifyCode'
]);
// sign up
Route::post('/user/sign-up', [
    'uses' => 'AuthController@signUp',
]);
// Forgot password
Route::post('/user/forgot-password', [
    'uses' => 'AuthController@forgotPassword',
]);
// reset password
Route::post('/user/reset-password', [
    'uses' => 'AuthController@resetPassword',
]);
// logout
Route::post('/user/logout', [
    'uses' => 'AuthController@logout',
]);

// get list of all the doctors
Route::post('/doctors/all', [
    'uses' => 'DoctorController@getAllDoctors',
]);

// get doctor profile
Route::post('/doctor/profile', [
    'uses' => 'DoctorController@getDoctorProfile',
]);

// filter data
Route::get('/doctors/filter', [
    'uses' => 'DoctorController@filter',
]);

// filter data
Route::get('/insurance-companies/list', [
    'uses' => 'InsuranceCompanyController@getAllInsuranceCompanies',
]);

// list of cities with provinces
Route::get('/doctors/provinces', [
    'uses' => 'DoctorController@getProvincesList',
]);

// list of cities with provinces
Route::get('/patient/plans', [
    'uses' => 'PatientController@getPatientPlans',
]);

// doctor clinics
Route::post('/doctor/clinics', [
    'uses' => 'DoctorController@getDoctorClinics',
    'as' => 'doctor-clinics'
]);
// clinic available days
Route::post('/clinic/days', [
    'uses' => 'ClinicController@getClinicDays',
]);
// get times in specific day
Route::post('/day/times', [
    'uses' => 'WorkingHourController@getDayWorkingHours',
]);

// get about us text
Route::get('/about_us', [
    'uses' => 'SettingController@aboutUs',
]);

// get contact us data
Route::get('/contact_us', [
    'uses' => 'SettingController@contactUs',
]);

Route::post('notification/push', [
    'uses' => 'NotificationController@createPushNotification',
]);


Route::post('/notification/user/count', [
    'uses' => 'NotificationController@getNotificationsCount',
]);

Route::post('/notification/user/list', [
    'uses' => 'NotificationController@notificationList',
]);

Route::post('/offers/all', [
    'uses' => 'OffersController@getAllOffers'
]);

Route::post('/offers/details', [
    'uses' => 'OffersController@getOfferDetails'
]);

Route::get('/offer/categories', [
    'uses' => 'OffersController@offer_categories'
]);

Route::get('/slider/ads', [
    'uses' => 'AdsController@adsSlider'
]);

Route::get('/slider/mobile/ads', [
    'uses' => 'AdsController@adsMobileSlider'
]);

Route::post('/offers/views/increment', [
    'uses' => 'OffersController@offerIncreaseViews'
]);

Route::post('/promo/add', [
    'uses' => 'PremiumPromoCodeController@addPromoCodeToUser'
]);

// market place
Route::post('/marketplace/products', [
    'uses' => 'MarketPlaceController@getProducts'
]);

// market place
Route::post('/marketplace/categories', [
    'uses' => 'MarketPlaceController@getCategories'
]);

// get doctor details for review
Route::post('/review/get/doctor', [
    'uses' => 'ReviewController@getReservationDetailsForReview'
]);

Route::post('/review/add', [
    'uses' => 'ReviewController@addReview'
]);

Route::post('/doctor/reviews', [
    'uses' => 'ReviewController@getReviewsList'
]);

Route::post('/review/ignore', [
    'uses' => 'ReviewController@IgnoreReview'
]);

Route::post('/notification/read', [
    'uses' => 'NotificationController@setNotificationRead'
]);

Route::post('doctor/services', [
    'uses' => 'DoctorController@getDoctorServices'
]);

/********************** Website Services ************************/


Route::group(['namespace' => 'System'], function () {
    // get doctors list
    Route::post('/site/doctors/list', [
        'uses' => 'DoctorController@siteDoctorsList',
    ]);

    Route::group(['middleware' => 'auth:api'], function () {
        // get my doctors list
        Route::post('/site/my-doctors/list', [
            'uses' => 'DoctorController@getMyDoctorsList',
        ]);
    });

    // get list of specialities
    Route::post('/specialities/list', [
        'uses' => 'SpecialityController@SiteSpecialitiesList',
    ]);
// get list of specialities
    Route::post('/speciality/doctors', [
        'uses' => 'SpecialityController@getSpecialtyDetails',
    ]);
    Route::post('/system/contact-mail', [
        'uses' => 'MailController@contactUs',
    ]);
    Route::post('/system/subscription', [
        'uses' => 'SubscriptionController@subscription',
    ]);
    Route::get('/system/policies', [
        'uses' => 'PolicyController@SitePolicies',
    ]);
});
