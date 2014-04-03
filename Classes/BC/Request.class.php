<?php
/**
 * Request.class.php 内容信息请求类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-27
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Request
{
    /**
     * Get请求数据
     *
     * @var array
     */
    private $get = array();

    /**
     * Post请求数据
     * @var array
     */
	private $post = array();

    /**
     * cookie数据
     *
     * @var array
     */
	private $cookie = array();

    /**
     * session数据
     *
     * @var array
     */
	private $session = array();

    /**
     * 文件上传数据
     *
     * @var array
     */
	private $files = array();

    /**
     * 运行环境信息
     *
     * @var array
     */
	private $server = array();
    
    /**
     * 缓存处理类实例化对象
     *
     * @var
     */
    protected $handler;

    /**
     * 构造函数
     * 过滤各项请求，将全局变量$_GET,$_POST等注册为request类的成员变量
     */
  	public function __construct()
    {
		Filter::request(); //过滤数据

		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->cookie = $_COOKIE;
		$this->session = $_SESSION;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}

    /**
     * 魔术方法 处理请求类型
     *
     * @throws BC_Exception
     * @param $handler
     * @return
     */
    public function __get($handler)
    {
        $handlerName = 'Request_Handler_'.ucfirst($handler);
        if (!class_exists($handlerName))
        {
            throw new BC_Exception('Request Handler:' . $handlerName . ' haven\'t found!');
        }

        $this->handler = new $handlerName();
        
        return $this->handler;
    }

    /**
	 * 获得请求的方法
     *
	 * @return string 返回POST\GET\DELETE等HTTP请求方式
	 */
	public function getRequestMethod()
    {
		return strtoupper($this->server['REQUEST_METHOD']);
	}

    /**
	 * 请求是否使用的是HTTPS安全链接
     *
	 * @return boolean 如果是安全请求则返回true否则返回false
	 */
	public function isSecure()
    {
		return !strcasecmp($this->server['HTTPS'], 'on');
	}

    /**
	 * 返回该请求是否为ajax请求
	 *
	 * @return boolean 如果是ajax请求将返回true,否则返回false
	 */
	public function isAjax()
    {
		return isset($this->server['HTTP_X_REQUESTED_WITH']) && !strcasecmp($this->server['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest');
	}

	/**
	 * 返回请求是否为GET请求类型
	 *
	 * @return boolean 如果请求是GET方式请求则返回true，否则返回false
	 */
	public function isGet()
    {
		return !strcasecmp($this->getRequestMethod(), 'GET');
	}

	/**
	 * 返回请求是否为POST请求类型
	 *
	 * @return boolean 如果请求是POST方式请求则返回true,否则返回false
	 */
	public function isPost()
    {
		return !strcasecmp($this->getRequestMethod(), 'POST');
	}

	/**
	 * 返回请求是否为PUT请求类型
	 *
	 * @return boolean 如果请求是PUT方式请求则返回true,否则返回false
	 */
	public function isPut()
    {
		return !strcasecmp($this->getRequestMethod(), 'PUT');
	}

	/**
	 * 返回请求是否为DELETE请求类型
	 *
	 * @return boolean 如果请求是DELETE方式请求则返回true,否则返回false
	 */
	public function isDelete()
    {
		return !strcasecmp($this->getRequestMethod(), 'Delete');
	}

    public function get()
    {

    }

    public function post()
    {

    }

    public function request()
    {
        
    }

    public function server()
    {

    }
}