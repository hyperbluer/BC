<?php
/**
 * Memcache.class.php Memcache缓存类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache_Handler_Memcache extends Cache_CacheAbstract
{
    /**
     * 缓存配置
     *
     * @var array
     */
    private $config;

    /**
     * 缓存实例化对象
     * @var \Memcache
     */
    private $handle;

    public function __construct($config)
    {
        if (!self::isSupport())
        {
            throw new BC_Exception('system havn\'t loaded memcache extension!');
        }
        
        $this->config = $config;
        !isset($this->config['host']) && $this->config['host'] = 'localhost';
        !isset($this->config['port']) && $this->config['port'] = 11211;

        $this->handle = new Memcache();

        if (!$this->handle->connect($this->config['host'], $this->config['port']))
        {
            throw new BC_Exception('Couldn\'t connect to memcache!');
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
        $expire = $expire ? time() + $expire : 0;
        
        return $this->handle->set($key, $value, false, $expire);
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
        return $this->handle->get($key);
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
        return $this->handle->delete($key);
    }

    /**
     * 清空缓存
     *
     * @see Cache_CacheAbstract::flush()
     * @param string $cacheType
     * @return bool
     */
    public function flush($cacheType = '') {
		return $this->handle->flush();
	}

    /**
     * 检测运行环境是否支持此缓存扩展
     *
     * @static
     * @return bool
     */
    public static function isSupport()
    {
        return extension_loaded('memcache');
    }
}