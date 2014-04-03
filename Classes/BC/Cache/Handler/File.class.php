<?php
/**
 * File.class.php 文件缓存类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache_Handler_File extends Cache_CacheAbstract
{
    /**
     * 缓存配置
     *
     * @var array
     */
    private $config;

    /**
     * 构造函数
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        !isset($this->config['path']) && $this->config['path'] = APP_CACHE_PATH;
        !isset($this->config['suffix']) && $this->config['suffix'] = '.cache';
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
	    File::mkDir($this->config['path']);

		$cacheName = $key.$this->config['suffix'];
		$cacheFile = $this->config['path'].$cacheName;

        $fileClass = new File($cacheFile, 'w');
	    $cacheSize = $fileClass->write($value);

	    return $cacheSize ? $cacheSize : 'false';
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
		$cacheName = $key.$this->config['suffix'];
		$cacheFile = $this->config['path'].$cacheName;

		if (!File::isFile($cacheFile))
			return false;

        $fileClass = new File($cacheFile);
        $data = $fileClass->read();

        return $data;
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
		$cacheName = $key.$this->config['suffix'];
		$cacheFile = $this->config['path'].$cacheName;

        if (!File::isFile($cacheFile))
            return false;

        return File::delete($cacheFile);

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
        return true;
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