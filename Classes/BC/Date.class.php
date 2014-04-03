<?php
/**
 * Captcha.class.php 图片验证码生成类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-24
 */
defined('IN_BC') or exit("Access Denied!");

class BC_Date
{
    /**
     * 获取时区
     * 
     * @static
     * @return string
     */
    public static function getTimeZone()
    {
        return function_exists('date_default_timezone_get') ? date_default_timezone_get() : date('e');
    }
    
    /**
     * 设置时区
     * 
     * @static
     * @param $timezone
     * @return void
     */
    public static function setTimezone($timezone)
    {
		function_exists('date_default_timezone_set') ? date_default_timezone_set($timezone) : putenv("TZ={$timezone}");
	}

    /**
     * 获取unix时间戳
     *
     * @static
     * @param null $dateTime 日期或unix时间戳,默认为null则以当前时间戳返回
     * @return int|null
     */
    public static function getTimeStamp($dateTime = null)
    {
		return $dateTime ? is_numeric($dateTime) ? $dateTime : strtotime($dateTime) : time();
	}

    /**
	 * 日期格式化
	 *
	 * @param string $format 目标格式,默认为null则以Y-m-d H:i:s格式输出
	 * @param int $dateTime 日期或unix时间戳，默认为null则用当前时间
	 * @return string
	 */
	public static function format($format = null, $dateTime = null)
    {
		return date($format ? $format : 'Y-m-d H:i:s', self::getTimeStamp($dateTime));
	}

    /**
	 * 获取UTC日期格式
	 *
	 * @param mixed $dateTime 时间，默认为null则获取当前时间
	 * @return string
	 */
	public static function getUTCDate($dateTime = null)
    {
		$oldTimezone = self::getTimezone();
		if ('UTC' !== strtoupper($oldTimezone))
        {
			self::setTimezone('UTC');
		}
		$date = date('D, d M y H:i:s e', self::getTimeStamp($dateTime));

		return $date;
	}
}