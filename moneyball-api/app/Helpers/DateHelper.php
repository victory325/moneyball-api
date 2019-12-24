<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Class DateHelper
 *
 * @package App\Helpers
 */
class DateHelper
{
    public static $dateFormat = 'Y-m-d';
    public static $timeFormat = 'H:i:s';
    public static $dateTimeFormat = 'Y-m-d H:i:s';

    /**
     * @param Carbon $date
     * @param bool   $tz
     *
     * @return string
     */
    public static function date(Carbon $date, bool $tz = true): string
    {
        return $tz ? self::tz($date)->format(self::$dateFormat) : $date->format(self::$dateFormat);
    }

    /**
     * @param Carbon $date
     * @param bool   $tz
     *
     * @return string
     */
    public static function time(Carbon $date, bool $tz = true): string
    {
        return $tz ? self::tz($date)->format(self::$timeFormat) : $date->format(self::$timeFormat);
    }

    /**
     * @param Carbon $date
     * @param bool   $tz
     *
     * @return string
     */
    public static function dt(Carbon $date, bool $tz = true): string
    {
        return $tz ? self::tz($date)->format(self::$dateTimeFormat) : $date->format(self::$dateTimeFormat);
    }

    /**
     * @param Carbon $date
     *
     * @return Carbon
     */
    public static function tz(Carbon $date): Carbon
    {
        return $date->setTimezone(config('app.timezone'));
    }
}
