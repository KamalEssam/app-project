<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use Illuminate\Support\Facades\Log;

class ParentRepository
{
    protected static $currentLocation = '';

    public static function logErr($msg)
    {
        self::$currentLocation = ' class [ ' . get_called_class() . ' ] and in method [ ' . debug_backtrace()[1]['function'] . " ] ";
        Log::error(" \n--------------------- \n this error in => " . self::$currentLocation . "\n says: \n $msg \n -------------------- \n");
    }
}
