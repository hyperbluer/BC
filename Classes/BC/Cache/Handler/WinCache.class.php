<?php
/**
 * WinCache.class.php WinCache缓存类
 *
 * 需安装PHP winCache扩展
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache_Handler_WinCache extends Cache_CacheAbstract
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
            throw new BC_Exception('Php Wincache extension is not available.');
        }
    }

    public function set($key, $value, $expire = 0)
    {
        return wincache_ucache_set($key, $value, $expire);
    }

    public function get($key)
    {
        return wincache_ucache_get($key);
    }

    public function delete($key)
    {
        return wincache_ucache_delete($key);
    }

    public function flush($cacheType = '')
    {
        return wincache_ucache_clear();
    }

    public static function isSupport()
    {
        return extension_loaded('wincache');
    }
}