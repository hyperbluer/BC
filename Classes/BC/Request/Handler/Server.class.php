<?php
/**
 * Server.class.php 请求Server类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-13
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Request_Handler_Server
{
    public function has($key)
	{
		return isset($_SERVER[$key]);
	}

	public function get($key)
	{
		if($this->has($key))
		{
			return $_SERVER[$key];
		}
		else
		{
			return false;
		}
	}

	public function set($key, $value)
	{
		$_SERVER[$key] = $value;
	}

	public function delete($key)
	{
		if($this->has($key))
		{
			unset($_SERVER[$key]);
		}
	}

	public function getAll()
	{
		return $_SERVER;
	}
}