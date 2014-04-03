<?php
/**
 * Default.class.php session缓存处理类
 *
 * 支持session储存为Cache/Handler/*所有处理方式
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-21
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Session_Handler_Cache
{
    protected $cachehandler;

    public function __construct()
    {
        $this->cachehandler = $this->getCacheHandler();
        
        @ini_set('session.save_handler', 'user');
        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
        Session::start();

    }
    
    public function open($savePath, $sessionName)
	{
        return true;
	}

	public function close()
	{
        return true;
	}

	public function read($sessionId)
	{
        return $this->cachehandler->get($sessionId);
	}

	public function write($sessionId, $data)
	{
        return $this->cachehandler->set($sessionId, $data);
	}

	public function destroy($sessionId)
	{
        return $this->cachehandler->delete($sessionId);
	}

	public function gc($lifetime)
	{
	}

    public function getCacheHandler()
    {
        $cacheHandler = isset(Session::$config['storage']) ? Session::$config['storage'] : '';
        $_cacheConfig = BC::config('cache');
        $cacheConfig = $cacheHandler && isset($_cacheConfig[$cacheHandler]) ? $_cacheConfig[$cacheHandler] : array();

        return Cache::instance($cacheConfig);
    }
}