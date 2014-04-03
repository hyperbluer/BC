<?php
/**
 * Request.class.php Http请求类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-28
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Http_Request
{
    /**
     * 请求url
     *
     * @var
     */
    protected $url;

    /**
     * 主机名或ip地址
     *
     * @var
     */
    protected $host;

    /**
     * 端口号
     *
     * @var string
     */
    protected $port = '80';

    /**
     * 请求路径，默认为根(/)
     *
     * @var string
     */
    protected $path = '/';

    /**
     * 请求方法，如GET/POST/PUT/DELETE
     *
     * @var
     */
	protected $method;

    /**
     * header头信息
     *
     * @var
     */
    protected $header;

    /**
     * 发送数据内容，一般用于POST/PUT方式请求
     *
     * @var
     */
	protected $data;

    /**
     * 发送cookie信息
     *
     * @var
     */
	protected $cookie;

    /**
     * 通讯协议tcp|tls|ssl
     *
     * @var
     */
	protected $scheme;

    /**
     * 是否为ssl通讯
     *
     * @var bool
     */
	protected $ssl = false;

    /**
     * 请求超时时间
     *
     * @var
     */
	protected $timeout;

    /**
     * 自定义返回处理
     *
     * @var
     */
	protected $callback;

    /**
     * 构造函数
     * 设置请求头信息，支持GET/POST/PUT/DELETE方式
     *
     * @throws BC_Exception
     * @param $url  请求url
     * @param string $method    请求方法，默认为GET
     * @param null $data    发送数据
     * @param array $header 自定义请求头
     * @param string $scheme    通讯协议
     */
    public function __construct($url, $method = 'GET', $data = null, $header = array(), $scheme = 'HTTP/1.1')
    {
        $actionName = 'set'.ucfirst($method);
        if (!$method || !method_exists (__CLASS__, $actionName))
        {
            throw new BC_Exception('System does not support '.$method.' request');
        }

        $this->setUrl($url);
		$this->setMethod($method);
        $this->setHeader($header);
		$this->setData($data);
		$this->setScheme($scheme);

        $this->$actionName();
    }

    /**
     * 设置GET方式请求头信息
     *
     * @return void
     */
    protected function setGet()
    {
        
    }

    /**
     * 设置POST方式请求头信息
     *
     * @return void
     */
    protected function setPost()
    {
        if(is_array($this->data))
		{
			$data = http_build_query($this->data, '', '&');

            if(($len = strlen($data)) > 0)
		    {
			    $this->addHeader('Content-Length', $len);

			    $this->data = $data;
		    }
		}
    }

    /**
     * 设置Put方式请求头信息
     *
     * @return void
     */
    protected function setPut()
    {
        $this->addHeader('X-HTTP-Method-Override', 'PUT');
        $this->setPost();
    }

    /**
     * 设置Delete方式请求头信息
     *
     * @return void
     */
    protected function setDelete()
    {
        $this->addHeader('X-HTTP-Method-Override', 'DELETE');
        $this->setPost();
    }

    /**
     * 自定义追加头信息
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function addHeader($key, $value)
	{
		$this->header[strtolower($key)] = $value;
	}

    /**
     * 解析请求的Url，提取url,host,port,path
     *
     * @param $url
     * @return void
     */
    public function setUrl($url)
	{
		$this->url = $url;

        if ($this->url)
        {
            $urlClass = new Url($this->url);
            $this->host = $urlClass->getHost();
            $this->port = $urlClass->getPort();
            if ($path = $urlClass->getPath())
                $this->path = $path;
        }
	}

    /**
     * 设置主机名
     *
     * @param string $host ip地址或网址
     * @return void
     */
    public function setHost($host = '')
    {
        $this->host = $host;
    }

    /**
     * 设置请求方式
     *
     * @param $method
     * @return void
     */
	public function setMethod($method)
	{
		$this->method = $method;
	}

    /**
     * 设置请求头
     *
     * @param array $header
     * @return void
     */
    public function setHeader(array $header)
	{
		$this->header = $header ? $header : array();
	}

    /**
     * 设置发送数据内容
     *
     * @param $data
     * @return void
     */
	public function setData($data)
	{
		$this->data = (string) $data;
	}

    /**
     * 设置发送cookie信息
     *
     * @param $cookie
     * @return void
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * 设置通讯协议
     *
     * @param $scheme
     * @return void
     */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
        
        if($this->scheme == 'https')
		{
			$this->setSSL(true);
		}
	}

    /**
     * 设置是否ssl通讯
     *
     * @param $ssl
     * @return void
     */
	public function setSSL($ssl)
	{
		$this->ssl = (boolean) $ssl;
	}

    /**
     * 设置超时时间
     *
     * @param $timeout
     * @return void
     */
    public function setTimeout($timeout)
	{
		$this->timeout = (integer) $timeout;
	}

    /**
     * 设置返回自定义响应
     *
     * @param $callback
     * @return void
     */
	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

    /**
     * 获取Url
     *
     * @return 
     */
    public function getUrl()
	{
		return $this->url;
	}

    /**
     * 获取主机名
     *
     * @return
     */
    public function getHost()
	{
		return $this->host;
	}

    /**
     * 获取端口号
     *
     * @return string
     */
    public function getPort()
	{
		return $this->port;
	}

    /**
     * 获取请求路径
     *
     * @return string
     */
    public function getPath()
	{
		return $this->path;
	}

    /**
     * 获取请求方法
     * 
     * @return
     */
	public function getMethod()
	{
		return $this->method;
	}

    /**
     * 获取请求头信息
     *
     * @return
     */
    public function getHeader()
	{
		return $this->header;
	}

    /**
     * 获取发送数据内容
     *
     * @return
     */
	public function getData()
	{
		return $this->data;
	}

    /**
     * 获取发送cookie信息
     *
     * @return
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * 获取通讯协议
     *
     * @return
     */
	public function getScheme()
	{
		return $this->scheme;
	}

    /**
     * 获取响应超时时间
     *
     * @return
     */
    public function getTimeout()
	{
		return $this->timeout;
	}

    /**
     * 获取自定义响应后操作
     *
     * @return
     */
    public function getCallback()
	{
		return $this->callback;
	}
}
