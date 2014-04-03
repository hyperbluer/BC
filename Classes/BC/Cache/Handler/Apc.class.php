<?php
/**
 * Apc.class.php Apc缓存类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache_Handler_Apc extends Cache_CacheAbstract
{
    /**
     * 构造函数
     *
     * @throws BC_Exception
     *
     */
    public function __construct()
    {
        if ( !self::isSupport())
        {
            throw new BC_Exception('Php APC extension is not available.');
        }
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
        return apc_store($key, $value);
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
        return apc_fetch($key);
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
        return apc_delete($key);
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
        return apc_clear_cache($cacheType);
    }

    /**
     * 检测运行环境是否支持此缓存扩展
     *
     * @static
     * @return bool
     */
    public static function isSupport()
    {
        return extension_loaded('apc');
    }
}