<?php
/**
 * Default.class.php session默认处理类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-21
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Session_Handler_Default
{
    public function __construct()
    {
        if(Session::$config['storage'])
        {
            @ini_set('session.save_handler', Session::$config['storage']);
            if (is_dir(APP_VAR_PATH) && Session::$config['storage'] == 'files')
            {
                session_save_path(APP_VAR_PATH.'sessions');
            }
        }

        Session::start();
    }

}