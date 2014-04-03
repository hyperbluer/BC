<?php
/**
 * Session.class.php session基类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-21
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Session
{
    /**
     * session实例
     *
     * @var
     */
    protected static $instance;

    /**
     * session处理句柄实例化对象
     *
     * @var \Session_Handler_Default
     */
    protected $handler;

    /**
     * session配置
     *
     * @var
     */
    public static $config;

    /**
     * 构造函数，session会话实例
     * 
     */
    public function __construct()
    {
        if ( Session::$instance === null )
        {
            Session::$config = BC::config('session');

            if ( ! isset(Session::$config['name']) || ! preg_match('#^(?=.*[a-z])[a-z0-9_]++$#iD', Session::$config['name']) )
            {
                Session::$config['name'] = 'PHPSESSID';
            }

            if ( isset(Session::$config['handler'])
                 && Session::$config['handler']
                 && class_exists('Session_Handler_' . ucfirst(Session::$config['handler']), true) )
            {
                $handlerName = 'Session_Handler_' . ucfirst(Session::$config['handler']);
                $this->handler = new $handlerName();
            }
            else
            {
                $this->handler = new Session_Handler_Default();
            }

            Session::$instance = $this;

        }
    }

    /**
     * session会话实例
     *
     * @static
     * @return object
     */
    public static function instance()
    {
        if ( Session::$instance == null )
        {
            new Session();
        }
        return Session::$instance;
    }

    /**
     * 获取某个session值
     *
     * @param $key
     * @return bool
     */
    public function get($key)
    {
        if($this->has($key))
		{
			return $_SESSION[$key];
		}
		else
		{
			return false;
		}
    }

    /**
     * 判断某个session是否存在
     *
     * @param $key
     * @return bool
     */
    public function has($key)
	{
		return isset($_SESSION[$key]);
	}

    /**
     * 设置session
     *
     * @param $keys
     * @param bool $value
     * @return bool
     */
    public function set($keys, $value = false)
    {
        if ( empty($keys) ) return false;

        if ( ! is_array($keys) )
        {
            $keys = array($keys => $value);
        }

        foreach ( $keys as $key => $value )
        {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * 删除所有session会话，session依然存在，只是临时将session值全部重置为空
     *
     * @return void
     */
    public function delete()
    {
        $args = func_get_args();

        foreach ( $args as $key )
        {
            unset($_SESSION[$key]);
        }
    }

    /**
     * 获取所有session
     *
     * @return array
     */
    public function getAll()
	{
		return $_SESSION;
	}

    /**
     * 销毁session会话
     *
     * @return void
     */
    public function destroy()
    {
        if ( session_id() !== '' )
        {
            session_destroy();

            $_SESSION = array();

            if(isset($_COOKIE[session_name()]))
		    {
			    setcookie(session_name(), '', time() - 3600);
		    }
        }
    }

    /**
     * 获取session处理句柄
     *
     * @return Session_Handler_Default
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * 开启session
     *
     * @static
     * @return void
     */
    public static function start()
    {
        session_name(Session::getName());
        @register_shutdown_function('session_write_close');
        self::setCookieParams();
        self::setGcMaxLifetime();
        session_start();
    }

    /**
     * 获取session name
     *
     * @static
     * @return string
     */
    public static function getName()
    {
        return Session::$config['name'];
    }

    /**
     * 设置session存储在cookie中的参数
     *
     * @return void
     */
    public static function setCookieParams()
	{
		$cookie	= session_get_cookie_params();
		$config = BC::config('cookie');

		!empty($config['lifetime']) && $cookie['lifetime'] = $config['lifetime'];
		!empty($config['cookie_path']) && $cookie['path'] = $config['cookie_path'];
		!empty($config['cookie_domain']) && $cookie['domain'] = $config['cookie_domain'];

		session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure']);
	}

    /**
     * 设置session超时时间
     *
     * @return string
     */
    public static function setGcMaxLifetime()
    {
        $config = BC::config('session');
        
        $config['gc_probability'] && ini_set('session.gc_probability', intval($config['gc_probability']));
        $config['gc_divisor'] && ini_set('session.gc_divisor', intval($config['gc_divisor']));
        $config['gc_maxlifetime'] && ini_set('session.gc_maxlifetime', $config['gc_maxlifetime']);
    }
}