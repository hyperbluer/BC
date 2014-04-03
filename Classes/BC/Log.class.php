<?php
/**
 * Log.class.php 日志类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-26
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Log
{
    /**
     * 读取日志
     *
     * @static
     * @param string $filename
     * @return string
     */
    public static function read($filename = '')
    {
        $filename = $filename ? $filename : 'system.log';
        $logFile = APP_LOG_PATH. $filename;
        
        $file = new File($logFile, 'r+');
        return $file->read();
    }

    /**
     * 写日志
     *
     * @static
     * @param $message
     * @param string $filename
     * @param string $mode
     * @return int
     */
    public static function write($message, $filename = '', $mode = 'a+')
    {
        $filename = $filename ? $filename : 'system.log';
        $logFile = APP_LOG_PATH. $filename;
        
        $file = new File($logFile, $mode);
        return $file->write($message);
    }

    /**
     * 删除日志
     *
     * @static
     * @param string $filename
     * @return bool
     */
    public static function delete($filename = '')
    {
        $filename = $filename ? $filename : 'system.log';
        $logFile = APP_LOG_PATH. $filename;

        return File::delete($logFile);
    }
}