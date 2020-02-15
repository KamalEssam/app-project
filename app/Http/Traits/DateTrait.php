<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Config;

trait DateTrait
{
    /**
     * to know if this day = today or tomorrow
     * @param $day
     * @return string
     */
    public static function getDayName($day)
    {
        switch ($day) {
            case self::getDateByFormat(self::getToday(), 'Y-m-d') :
                $day = trans('lang.today');
                break;

            case self::getDateByFormat(self::getTomorrow(), 'Y-m-d') :
                $day = trans('lang.tomorrow');
                break;

            default :
                return $day;
        }
        return $day;
    }

    /**
     * get day name by index
     * @param $day_index
     * @return mixed
     */
    public static function getDayNameByIndex($day_index)
    {
        foreach (Config::get('lists.days') as $single_day) {
            if ($day_index == $single_day['day']) {
                $day_name = $single_day[app()->getLocale() . '_name'];
                break;
            }
        }
        return $day_name;
    }

    /**
     * get index of the day
     * @param $day
     * @return mixed
     */
    public static function getDayIndex($day)
    {
        //  get index day from days list in config
        $day_parsing = Carbon::parse($day, "Africa/Cairo");

        //get name of day (sunday,monday,....)
        $day_name = self::getDateByFormat($day_parsing, 'l');

        foreach (Config::get('lists.days') as $single_day) {
            if ($day_name == $single_day['en_name']) {
                $index = $single_day['day'];
                break;
            }
        }
        return $index;
    }

    /**
     * parse date
     * @param $date
     * @return Carbon
     */
    public static function parseDate($date)
    {
        return Carbon::parse($date, 'Africa/Cairo');
    }

    /**
     * get today
     * @return Carbon
     */
    public static function getToday()
    {
        return Carbon::today('Africa/Cairo');
    }

    /**
     * get today
     * @return Carbon
     */
    public static function getTomorrow()
    {
        return Carbon::tomorrow('Africa/Cairo');
    }

    /**
     * @param $date
     * @param $format
     * @return string
     */
    public static function getDateByFormat($date, $format)
    {
        return Carbon::parse($date, 'Africa/Cairo')->format($format);
    }

    /**
     * get day formatted with am and pm
     * @param $time
     * @param string $format
     * @return string
     */
    public static function getTimeByFormat($time, $format)
    {
        return Carbon::parse($time, 'Africa/Cairo')->format($format);
        // return date($format, strtotime($time));
    }

    /**
     * @param $date_from
     * @return string
     */
    public static function readableDate($date_from)
    {
        $result = Carbon::createFromTimeStamp(strtotime($date_from), 'Africa/Cairo')->diffForHumans();
        return $result;
    }

    /**
     * add no of days to specific day
     * @param $add_from_day
     * @param $no_of_days
     * @return mixed
     */
    public static function addDays($add_from_day, $no_of_days)
    {
        return $add_from_day->addDays($no_of_days);
    }

    /**
     * @param $format
     * @return string
     */
    public static function getTodayFormat($format)
    {
        return Carbon::now('Africa/Cairo')->format($format);
    }

    /**
     *  convert english numbers to arabic
     *
     * @param $string
     * @return string
     */
    public static function enToAr($string, $removeZeros = true)
    {
        $string = $removeZeros ? intval($string) : $string;
        return strtr($string, array('0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤', '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩'));
    }

    /**
     *  translate time range
     *
     * @param $time_range
     * @return string
     */
    public static function TranslateTimeRange($time_range)
    {
        if (in_array($time_range, ['am', 'AM', 'A M', 'a m'])) {
            return 'ص';
        }
        return 'م';
    }
}
