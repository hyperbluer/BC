<?php
/**
 * Post.class.php 请求Post类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-13
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Request_Handler_Post extends Request_RequestAbstract
{
	public function has($key)
	{
		return isset($_POST[$key]);
	}

	public function get($key)
	{
		if($this->has($key))
		{
			return $_POST[$key];
		}
		else
		{
			return false;
		}
	}

	public function set($key, $value)
	{
		$_POST[$key] = $value;
	}

	public function delete($key)
	{
		if($this->has($key))
		{
			unset($_POST[$key]);
		}
	}

	public function getAll()
	{
		return $_POST;
	}
}