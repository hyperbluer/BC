<?php
/**
 * Get.class.php 请求Get类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-13
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Request_Handler_Get extends Request_RequestAbstract
{

	public function has($key)
	{
		return isset($_GET[$key]);
	}

	public function get($key)
	{
		if($this->has($key))
		{
			return $_GET[$key];
		}
		else
		{
			return false;
		}
	}

	public function set($key, $value)
	{
		$_GET[$key] = $value;
	}

	public function delete($key)
	{
		if($this->has($key))
		{
			unset($_GET[$key]);
		}
	}

	public function getAll()
	{
		return $_GET;
	}
}