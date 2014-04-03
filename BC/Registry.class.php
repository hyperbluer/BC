<?php
/**
 * Registry.php 注册核心类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */

defined('IN_BC') or exit("Access Denied!");

final class BC_Registry
{
    /**
     * 注册集合
     *
     * @var array
     */
	private $data = array();

    /**
     * 获取键值
     *
     * @param $key
     * @return null
     */
	public function get($key)
    {
		return (isset($this->data[$key]) ? $this->data[$key] : NULL);
	}

    /**
     * 注册键值对
     *
     * @param $key
     * @param $value
     * @return void
     */
	public function set($key, $value)
    {
		$this->data[$key] = $value;
	}

    /**
     * 判断是否存在此键名
     *
     * @param $key
     * @return bool
     */
	public function has($key)
    {
    	return isset($this->data[$key]);
  	}
}