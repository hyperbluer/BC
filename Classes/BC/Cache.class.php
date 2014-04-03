<?php
/**
 * Cache.class.php 缓存工厂类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-19
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache
{
    /**
     * 缓存处理类实例化对象
     *
     * @var
     */
    protected $handler;

    /**
     * 缓存实例，静态存储。
     *
     * @var array
     */
    public static $instances = array();

    /**
     * 构造函数
     *
     * @throws BC_Exception
     * @param array $config 缓存配置数组，默认为文件缓存
     */
    public function __construct($config = array())
    {
        if (!isset($config['handler']))
        {
            $config['handler'] = BC::config('cache_handler') ? BC::config('cache_handler') : 'file';
        }
        $handlerName = 'Cache_Handler_'.ucfirst($config['handler']);
        if (!class_exists($handlerName))
        {
            throw new BC_Exception('Cache Handler:' . $handlerName . ' haven\'t found!');
        }

        $this->handler = new $handlerName($config);

        return $this->handler;

    }

    /**
     * 获取缓存实例，采取单例模式，避免运行中不必要的多次实例化缓存
     *
     * @static
     * @param array $config
     * @return object
     */
    public static function instance($config = array())
    {
        if ( is_array($config) )
        {
            $key = 'BC_' . md5(serialize($config));
        }
        else
        {
            $key = 'BC_Cache';
        }
        if ( ! isset(Cache::$instances[$key]) )
        {
            Cache::$instances[$key] = new Cache($config);
        }
        return Cache::$instances[$key];
    }

    /**
     * 获取缓存处理类实例化对象
     *
     * @return 
     */
    public function getHandler()
    {
        return $this->handler;
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
        $value = json_encode($value);
        return $this->handler->set($key, $value);
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
        return json_decode($this->handler->get($key));
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
        return $this->handler->delete($key);
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
        return  $this->handler->flush($cacheType);
    }

}