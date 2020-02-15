<?php

namespace App\Http\Controllers;


use App\Http\Traits\MailTrait;
use Flashy;
use Log;

class WebController extends Controller
{
    use MailTrait;

    // here we will include all the helpers for the web controllers part


    // Roles number as constant
    const ROLE_DOCTOR = 1;
    const ROLE_ASSISTANT = 2;
    const ROLE_USER = 3;
    const ROLE_RK_ADMIN = 4;
    const ROLE_RK_SUPER_ADMIN = 5;
    const ROLE_RK_SALES = 6;
    const ROLE_BRAND = 7;

    const ACTIVE = 1;

    const TRUE = 1;
    const FALSE = 0;

    // lang
    const LANG_EN = 1;
    const LANG_AR = 2;

    const ADD_DOCTOR_TO_LIST = 1;
    const REMOVE_DOCTOR_FROM_LIST = 0;

    // reservation status
    const R_STATUS_PENDING = 0;
    const R_STATUS_APPROVED = 1;
    const R_STATUS_CANCELED = 2;
    const R_STATUS_ATTENDED = 3;
    const R_STATUS_MISSED = 4;

    // status
    const STATUS_OK = 1;
    const STATUS_ERR = 0;
    const STATUS_NONE = -1;

    // queue and interval
    const PATTERN_INTERVAL = 0;
    const PATTERN_QUEUE = 1;

    // methods
    const METHOD_INDEX = 0;
    const METHOD_EDIT = 1;

    const NO_OF_DAYS = 4;
    //Reservation Type
    const TYPE_CHECK_UP = 0;
    const TYPE_FOLLOW_UP = 1;
    // doctor type
    const ACCOUNT_TYPE_SINGLE = 0;
    const ACCOUNT_TYPE_POLY = 1;
    /**
     *  log error message
     *
     * @param string $msg
     * @param string $page
     */

    protected static $currentLocation = '';

    /**
     *  log error message in log file in case of try and catch
     *
     * @param string $msg
     */
    public static function logErr($msg)
    {
        self::$currentLocation = " class [ " . get_called_class() . " ] and in method [ " . debug_backtrace()[1]['function'] . " ] ";
        Log::error(" \n--------------------- \n this error in => " . self::$currentLocation . "\n says: \n $msg \n -------------------- \n");
    }

    public static function catchExceptions($msg)
    {
        self::logErr($msg);
        // send mail to developer
        return false;
    }


    /**
     *  method used when call destroy method in all web controllers
     *
     * @param $model
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    protected function deleteItem($model, $id)
    {
        $item = $model::find($id);
        if (!$item) {
            return response()->json(['msg' => false], 200);
        }
        try {
            $item->delete();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return response()->json(['msg' => false], 200);
        }
        return response()->json(['msg' => true], 200);
    }

    /**
     *  redirect to route || redirect back
     *  print ok message || error message || no message at all
     *
     * @param $message_type
     * @param $msg
     * @param string $route
     * @param array $parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function messageAndRedirect($message_type, $msg = '', $route = '', $parameters = [])
    {
        if ($message_type == self::STATUS_OK) {
            Flashy::message($msg);
        } else if ($message_type == self::STATUS_ERR) {
            Flashy::error($msg);
        }
        if (empty($route)) {
            return redirect()->back();
        }
        return redirect()->route($route, $parameters);
    }

    /**
     * return view and flashy message
     * @param $message_type
     * @param string $msg
     * @param $view
     * @param $parameters
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function messageAndReturnView($message_type, $msg = '', $view , $parameters)
    {
        if ($message_type == self::STATUS_OK) {
            Flashy::message($msg);
        } else if ($message_type == self::STATUS_ERR) {
            Flashy::error($msg);
        }
        return view($view, $parameters);
    }

    /**
     * send mail method
     * @param $to
     * @param $subject
     * @param $view
     * @param string $more_data
     * @return bool
     */
    public function sendMail($to, $subject, $view, $more_data = '')
    {
        try {
            $data = [
                'to' => $to,
                'subject' => $subject,
                'view' => $view,
                'news' => $more_data,
            ];
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
        try {
            $this->sendMailTraitFun($data);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * if not value put n/a
     * @param $value
     */
    public static function getProperty($value)
    {
        if (!$value || $value == null || empty($value || !isset($value))) {
            echo 'N/A';
        } else {
            echo $value;
        }
    }

    /**
     * generate random password
     * @return bool|string
     */
    public static function generatePassword(){
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, 8);
    }

}
