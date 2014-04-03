<?php
/**
 * Request.class.php 请求Request类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-13
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Request_Handler_Request
{
    public function has($key)
	{
		return isset($_REQUEST[$key]);
	}

	public function get($key)
	{
		if($this->has($key))
		{
			return $_REQUEST[$key];
		}
		else
		{
			return false;
		}
	}

	public function set($key, $value)
	{
		$_REQUEST[$key] = $value;
	}

	public function delete($key)
	{
		if($this->has($key))
		{
			unset($_REQUEST[$key]);
		}
	}

	public function getAll()
	{
		return $_REQUEST;
	}
}