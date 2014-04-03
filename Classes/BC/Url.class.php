<?php
/**
 * Url.class.php Url处理类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Url
{
    /**
     * 传输协议，http/https等
     *
     * @var
     */
    protected $scheme;

    /**
     * 主机名或ip地址
     *
     * @var
     */
    protected $host;

    /**
     * 端口
     *
     * @var
     */
	protected $port;

    /**
     * 权限访问所需用户名
     *
     * @var
     */
	protected $user;

    /**
     * 权限访问所需密码
     *
     * @var
     */
	protected $pass;

    /**
     * 相对路径
     *
     * @var
     */
    protected $path;

    /**
     * 参数
     *
     * @var
     */
	protected $query;

    /**
     * 描点
     * 
     * @var
     */
    protected $fragment;

    /**
     * 构造函数，解析url中，设置各项参数值
     *
     * @param $url
     */
	public function __construct($url)
	{
		$parts = self::parse($url);

        $this->setScheme($parts['scheme']);
		$this->setHost($parts['host']);
		$this->setPort($parts['port']);
		$this->setUser($parts['user']);
		$this->setPass($parts['pass']);
		$this->setPath($parts['path']);
		$this->setQuery($parts['query']);
        $this->setFragment($parts['fragment']);
	}

    /**
     * 获取当前页面路径URL地址
     *
     * @static
     * @return string
     */
    public static function getCurrentUrl()
    {
        $protocol = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	    $phpSelf = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	    $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	    $relateUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
                    $phpSelf.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $pathInfo);

        return $protocol.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relateUrl;
    }

    /**
     * 获取处理后URL
     *
     * @throws BC_Exception
     * @return string
     */
    public function getUrl()
	{
		$url = $this->scheme . '://';

		if(!empty($this->user) && !empty($this->pass))
		{
			$url.= $this->user . ':' . $this->pass . '@';
		}

		if(!empty($this->host))
		{
			$url.= $this->host;
		}
		else
		{
			throw new BC_Exception('No host set');
		}

		if(!empty($this->port) && $this->port != 80 && $this->port != 443)
		{
			$url.= ':' . $this->port;
		}

		if(!empty($this->path))
		{
			$url.= '/' . ltrim($this->path, '/');
		}

		if(!empty($this->query))
		{
			$url.= '?' . http_build_query($this->query, '', '&');
		}

		if(!empty($this->fragment))
		{
			$url.= '#' . $this->fragment;
		}
        
		return $url;
	}

    /**
     * 获取通讯协议
     *
     * @return string
     */
    public function getScheme()
	{
		return $this->scheme;
	}

    /**
     * 获取主机名或ip地址
     *
     * @return string
     */
    public function getHost()
	{
		return $this->host;
	}

    /**
     * 获取端口
     *
     * @return string
     */
	public function getPort()
	{
		return $this->port;
	}

    /**
     * 获取权限访问所需用户名
     *
     * @return string
     */
	public function getUser()
	{
		return $this->user;
	}

    /**
     * 获取权限访问所需密码
     *
     * @return string
     */
	public function getPass()
	{
		return $this->pass;
	}

    /**
     * 获取URL中相对路径部分
     *
     * @return string
     */
    public function getPath()
	{
		return $this->path;
	}

    /**
     * 获取参数部分
     *
     * @return string
     */
	public function getQuery()
	{
		return $this->query;
	}

    /**
     * 获取描点部分
     *
     * @return string
     */
    public function getFragment()
	{
		return $this->fragment;
	}

    /**
     * 设置传输协议头
     *
     * @param $scheme
     * @return void
     */
    public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}

    /**
     * 设置主机名
     *
     * @param $host 可以为网址或ip地址
     * @return void
     */
	public function setHost($host)
	{
		$this->host = $host;
	}

    /**
     * 设置端口号
     *
     * @param $port
     * @return void
     */
	public function setPort($port)
	{
		$this->port = $port;
	}

    /**
     * 设置权限访问所需用户名
     *
     * @param $user
     * @return void
     */
	public function setUser($user)
	{
		$this->user = $user;
	}

    /**
     * 设置权限访问所需密码
     *
     * @param $pass
     * @return void
     */
	public function setPass($pass)
	{
		$this->pass = $pass;
	}

    /**
     * 设置URL中相对路径部分
     *
     * @param $path
     * @return void
     */
    public function setPath($path)
	{
		$this->path = $path;
	}

    /**
     * 设置URL中参数部分
     *
     * @param $query
     * @return void
     */
	public function setQuery($query)
	{
		$this->query = $query;
	}

    /**
     * 设置描点部分
     *
     * @param $fragment
     * @return void
     */
    public function setFragment($fragment)
	{
		$this->fragment = $fragment;
	}

    /**
     * 获取Url中参数部分
     *
     * @see Url::getQuery()
     * @return string
     */
    public function getParams()
	{
		return $this->getQuery();
	}

    /**
     * 批量增加参数键值对
     *
     * @param array $params
     * @return void
     */
	public function addParams(array $params)
	{
		foreach($params as $k => $v)
		{
			$this->addParam($k, $v);
		}
	}

    /**
     * 获取Url中参数部分单个参数值
     *
     * @param $key
     * @return null
     */
	public function getParam($key)
	{
		return isset($this->query[$key]) ? $this->query[$key] : null;
	}

    /**
     * 增加单一键值对
     *
     * @param $key
     * @param $value
     * @param bool $replace 是否替换已存在的参数，默认为跳过
     * @return void
     */
	public function addParam($key, $value, $replace = false)
	{
		if($replace === false)
		{
			if(!isset($this->query[$key]))
			{
				$this->query[$key] = $value;
			}
		}
		else
		{
			$this->query[$key] = $value;
		}
	}

    /**
     * 删除单一参数键值对
     *
     * @param $key
     * @return void
     */
	public function deleteParam($key)
	{
		if(isset($this->query[$key]))
		{
			unset($this->query[$key]);
		}
	}

    /**
     * 判断是否存在某参数
     *
     * @param $key
     * @return bool
     */
	public function issetParam($key)
	{
		return isset($this->query[$key]);
	}

    /**
     * 解析Url
     *
     * @static
     * @throws BC_Exception
     * @param $url
     * @return array
     */
	public static function parse($url)
	{
		$url = (string) $url;

		// validate url format
		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false)
		{
			throw new BC_Exception('Invalid url syntax');
		}

		$matches = parse_url($url);

		$parts = array(
			'scheme'    => isset($matches['scheme'])   ? $matches['scheme']       : null,
			'authority' => null,
			'host'      => isset($matches['host'])     ? $matches['host']         : null,
			'port'      => isset($matches['port'])     ? intval($matches['port']) : null,
			'user'      => isset($matches['user'])     ? $matches['user']         : null,
			'pass'      => isset($matches['pass'])     ? $matches['pass']         : null,
			'path'      => isset($matches['path'])     ? $matches['path']         : null,
			'query'     => isset($matches['query'])    ? $matches['query']        : array(),
			'fragment'  => isset($matches['fragment']) ? $matches['fragment']     : null,
		);

		// build authority
		$authority = '';

		if($parts['user'] !== null)
		{
			$authority.= $parts['user'];

			if($parts['pass'] !== null)
			{
				$authority.= ':' . $parts['pass'];
			}

			$authority.= '@';
		}

		$authority.= $parts['host'];

		if($parts['port'] !== null)
		{
			$authority.= ':' . $parts['port'];
		}

		$parts['authority'] = $authority;

		// parse params
		if(!empty($parts['query']))
		{
			$query = array();

			parse_str(str_replace('&amp;', '&', $parts['query']), $query);

			$parts['query'] = $query;
		}

		return $parts;
	}
}