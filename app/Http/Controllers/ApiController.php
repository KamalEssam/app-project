<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Api\AuthRepository;
use Carbon\Carbon;


class ApiController extends Controller
{
    // here we will include all the helpers for the api controllers part


    // error codes
    const CODE_OK = 20;
    const CODE_CREATED = 21;
    const CODE_FAILED = 22;
    const CODE_NOT_FOUND = 23;
    const CODE_VALIDATION = 24;
    const CODE_INTERNAL_ERR = 25;
    const CODE_RECORD_EXISTS = 26;
    const CODE_NOT_ACTIVE = 27;
    const CODE_METHOD_NOT_ALLOWED = 28;
    const CODE_NOT_MATCH = 29;
    const CODE_SAME_PASSWORD = 30;
    const CODE_UNAUTHORIZED = 42;
    const CODE_NO_CLINICS = 35;
    const CODE_INVALID_PROMO = 55;

    // Roles number as constant
    const ROLE_DOCTOR = 1;
    const ROLE_ASSISTANT = 2;
    const ROLE_USER = 3;
    const ROLE_RK_ADMIN = 4;
    const ROLE_RK_SUPER_ADMIN = 5;

    const ACTIVE = 1;

    const TRUE = 1;
    const FALSE = 0;
    // lang
    const LANG_EN = 1;
    const LANG_AR = 2;

    // if doctor have clinics or not
    const ACCOUNT_PUBLISHED = 1;

    // add and remove doctor from list
    const ADD_DOCTOR_TO_LIST = 1;
    const REMOVE_DOCTOR_FROM_LIST = 0;

    // reservation status
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_CANCELED = 2;
    const STATUS_ATTENDED = 3;
    const STATUS_MISSED = 4;

    //Clinic pattern
    const PATTERN_INTERVAL = 0;
    const PATTERN_QUEUE = 1;

    //Clinic pattern
    const VAT_NOT_INCLUDED = 0;
    const VAT_INCLUDED = 1;

    //Payment Method
    const METHOD_CASH = 0;
    const METHOD_ONLINE = 1;
    const METHOD_INSTALLMENT = 2;

    //Reservation Type
    const TYPE_CHECK_UP = 0;
    const TYPE_FOLLOW_UP = 1;
    // doctor type
    const ACCOUNT_TYPE_SINGLE = 0;
    const ACCOUNT_TYPE_POLY = 1;
    //
    /**
     *  log error message
     *
     * @param string $msg
     * @param string $page
     */

    protected static $currentLocation = '';
    protected static $lang = '';


    public static function getToday()
    {
        return Carbon::today();
    }

    /**
     *  log error message in log file in case of try and catch
     *
     * @param string $msg
     */
    public static function logErr($msg)
    {
        self::$currentLocation = ' class [ ' . get_called_class() . ' ] and in method [ ' . debug_backtrace()[1]['function'] . ' ] ';
        \Log::error(" \n--------------------- \n this error in => " . self::$currentLocation . "\n says: \n $msg \n -------------------- \n");
    }


    public static function catchExceptions($msg)
    {
        self::logErr($msg);
        // send mail to developer
        return false;
    }

    public static function jsonResponse($status, $error_code, $message, $validation = "", $response = "", $token = "")
    {
        $response = ($response === "") ? new \stdClass() : $response;
        $validation = ($validation == "") ? new \stdClass() : $validation;

        return response()->json([
            'Error' => [
                'status' => $status,
                'code' => $error_code,
                'validation' => $validation,
                'desc' => $message,
                'token' => $token
            ],
            'Response' => $response,
        ], 200);
    }

    public function setLang($request)
    {
        // update Language
        if (!in_array($request->headers->get('Lang'), ['en', 'ar'])) {
            self::$lang = 'en';
        } else {
            self::$lang = $request->headers->get('Lang');
        }
        app()->setLocale(self::$lang);
        $authenticatedUser = auth()->guard('api')->user();
        if ($authenticatedUser && $authenticatedUser->lang != self::$lang) {
            // update user language
            (new AuthRepository())->updateColumn($authenticatedUser, 'lang', self::$lang);
        }
    }
}
