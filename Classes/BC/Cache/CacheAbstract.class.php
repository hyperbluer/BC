<?php
/**
 * CacheAbstract.class.php 缓存抽象类
 *
 * 统一规范缓存
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */
 
defined('IN_BC') or exit("Access Denied!");

abstract class BC_Cache_CacheAbstract
{

    /**
     * 键值对 写入操作
     *
     * @abstract
     * @param $key  键名
     * @param $value    赋值
     * @param int $expire   过期时间
     * @return void
     */
	abstract public function set($key, $value, $expire = 0);

    /**
     * 键值对 读取操作
     *
     * @abstract
     * @param $key
     * @return void
     */
	abstract public function get($key);

    /**
     * 键值对 删除操作
     *
     * @abstract
     * @param $key
     * @return void
     */
	abstract public function delete($key);

    /**
     * 清空缓存
     *
     * @abstract
     * @param string $cacheType
     * @return void
     */
    abstract public function flush($cacheType = '');

}
