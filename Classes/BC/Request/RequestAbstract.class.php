<?php
/**
 * RequestAbstract.class.php 请求类型抽象类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-27
 */

defined('IN_BC') or exit("Access Denied!");

abstract class BC_Request_RequestAbstract
{

	abstract public function has($key);

	abstract public function get($key);

	abstract public function set($key, $value);

	abstract public function delete($key);

	abstract public function getAll();
}