<?php
/**
 * Database.class.php 数据库缓存类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache_Handler_Database extends Cache_CacheAbstract
{
    /**
	 * 缓存表名
	 *
	 * @var string
	 */
	private $table = 'cache';

	/**
	 * 缓存表的键字段
	 *
	 * @var string
	 */
	private $keyField = 'key';

	/**
	 * 缓存表的值字段
	 *
	 * @var string
	 */
	private $valueField = 'value';

	/**
	 * 缓存表过期时间字段
	 *
	 * @var string
	 */
	private $expireField = 'expire';

    /**
     * 配置
     *
     * @var array
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 键值对 写入操作
     *
     * @see Cache_CacheAbstract::set()
     * @param $key
     * @param $value
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire = 0)
    {
        
    }

    /**
     * 键值对 读取操作
     *
     * @see Cache_CacheAbstract::get()
     * @param $key
     * @return mixed
     */
    public function get($key)
    {

    }

    /**
     * 键值对 删除操作
     *
     * @see Cache_CacheAbstract::delete()
     * @param $key
     * @return bool|string[]
     */
    public function delete($key)
    {

    }

    /**
     * 清空缓存
     *
     * @see Cache_CacheAbstract::flush()
     * @param string $cacheType
     * @return bool
     */
    public function flush($cacheType = '')
    {

    }

    /**
     * 检测运行环境是否支持此缓存扩展
     *
     * @static
     * @return bool
     */
    public static function isSupport()
    {
        return true;
    }
}