<?php
//载入初始化定义文件
require_once('Init.php');

/**
 * BC.php 框架初始化核心类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-08
 */
class BC
{
    public static $includePath = array(); //类库引入路径
    public static $module; //模块
    public static $controller; //控制器
    public static $action; //动作

    private static $loader; //装载器
    private static $config; //应用配置

    /**
     * 框架入口方法
     * @static
     * @return void
     */
    public static function run()
    {
        BC::loader()->config =  new BC_Config(); //注册配置类
        BC::loader()->response =  new Response(); //注册响应类
        BC::loader()->lang = new BC_Lang(); //注册语言类
        BC::loader()->tpl = new Template(); //注册模板类

        //加载APP配置
        $_configFiles = array('config', 'cache', 'database', 'route');
        BC::loader()->config->load($_configFiles);
        self::$config = self::$loader->config->getAll();
		
		//加载所有已激活插件，注册默认钩子
		BC_Plugin::instance()->load();

		//加载APP自定义加载项
		self::autoLoadFromConfig();
        //包含函数库
        require_once(BC_FUNC_PATH.'Extend.func.php'); //自定义扩展函数库
        require_once(BC_FUNC_PATH.'Global.func.php'); //系统默认通用函数库
        
        //定义项目根文件及静态文件相对目录
        !defined('ROOT_PATH') && define('ROOT_PATH', rtrim(BC::config('root_path'), '/').'/'); //静态文件相对目录
        $_assetsPath = BC::config('assets_path') ? rtrim(BC::config('assets_path'), '/').'/' : ROOT_PATH;
        !defined('ASSETS_PATH') && define('ASSETS_PATH', $_assetsPath.'assets/'); //静态文件相对目录
        !defined('ASSETS_ADMIN_PATH') && define('ASSETS_ADMIN_PATH', ASSETS_PATH.'admin/'); //管理后台静态文件相对目录
        !defined('JS_PATH') && define('JS_PATH', ASSETS_PATH.'js/'); //前端JS文件相对目录
        !defined('CSS_PATH') && define('CSS_PATH', ASSETS_PATH.'css/'); //前端css文件相对目录
        !defined('IMG_PATH') && define('IMG_PATH', ASSETS_PATH.'images/'); //前端图片文件相对目录
        !defined('UPLOAD_PATH') && define('UPLOAD_PATH', ASSETS_PATH.'upload/'); //前端上传文件相对目录

        //错误状态配置
        self::setErrorReport();
        //开启文件存储session
        BC::loader()->session = Session::instance();

        BC::loader()->request =  new Request(); //注册请求类

        //设置时区
        $timezone = BC::config('time_zone') ? BC::config('time_zone') : 'Etc/GMT-8';
        Date::setTimezone($timezone);

        //设置语言
        BC::loader()->lang->lang(BC::config('lang'));
        BC::loader()->lang->load('lang');

        //URL解析
        self::route();
		
		//设置文件输出字符集
        $charset = BC::config('charset') ? BC::config('charset') : 'utf-8';
        BC::loader()->response->addHeader('Content-type:text/html;charset='.$charset); 
		
		//执行当前路由映射模块-控制器-方法
		$controllerFile = APP_MOD_PATH.self::$module.DS.'controllers'.DS.self::$controller.'.php';
		if (file_exists($controllerFile))
		{
			$className = 'Controller_'.ucfirst(self::$module).'_'.ucfirst(self::$controller);
			include $controllerFile;
			$controller = new $className;
		
			if (method_exists($controller, self::$action))
			{
				ob_start();
				call_user_func(array($controller, self::$action));
				$output = ob_get_contents();
				ob_end_clean();

				BC::loader()->response->setOutput($output); //设置响应内容信息
				BC::loader()->response->output(); //输出页面
			}
			else
			{
				throw new BC_Exception('Action ['.self::$module.'/'.self::$controller.'/'.self::$action.'] does not exist.', '404');
			}
		}
		else
		{
			throw new BC_Exception('Controller ['.self::$module.'/'.self::$controller.'] does not exist.', '404');
		}
		
        //运行时间
        self::getRunTime();
        
        exit;
    }

    /**
     * 获取装载器
     *
     * @static
     * @return
     */
    public static function loader()
    {
        if (self::$loader === null)
            self::$loader = new BC_Loader(new BC_Registry()); //实例化装载类
        
        return self::$loader;
    }

    /**
     * 获取配置信息
     *
     * <code>
     * BC::config('cache'); //获取cache配置项
     * BC::config('cache/redis'); //获取redis缓存配置项
     * BC::config('cache/redis/host'); //获取redis缓存的主机名
     * ...
     * </code>
     * 
     * @static
     * @param null|string $key
     * @return null|array|string
     */
    public static function config($key = null)
    {
        $key = trim(trim($key), '/');
        if ($key !== null)
        {
            if (stripos($key, '/') !== false)
            {
                $keyArray = explode('/', $key);
            }
            else
            {
                $keyArray = array($key);
            }

            $data = self::$config;
            foreach ($keyArray as $k => $v)
            {
                if (!isset($data[$v]))
                {
                    return null;
                    break;
                }
                $data = $data[$v];
            }

            return $data;
        }
        else
        {
            return self::$config;
        }

    }

    /**
     * 错误状态配置 - 自定义函数响应错误信息
     *
     * @static
     * @return void
     */
    private static function setErrorReport()
    {
        error_reporting(0);
        set_error_handler('BC_Exception::errorHandler');
    }

    /**
     * 获取pathinfo
     * @static
     * @return array
     */
    private static function route()
    {
        $pathInfoArray = array();
        $_server = BC::loader()->request->server->getAll();
        if ( isset($_server['PATH_INFO']) )
        {
            $pathInfo = $_server['PATH_INFO'];
        }
        else
        {
            if ( isset($_server['REQUEST_URI']) )
            {
                $requestUri = $_server['REQUEST_URI'];
				if ($_server['SCRIPT_NAME'])
				{
					$requestUri = ltrim(substr($requestUri, strlen(dirname($_server['SCRIPT_NAME']))), '/');
				}
                elseif ( BC::config('root_path') )
                {
                    $requestUri = substr($requestUri, strlen(BC::config('root_path')));
                }

                // 移除查询参数
                list ( $pathInfo ) = explode( '?', $requestUri, 2 );
				
            }
            elseif ( isset($_server['PHP_SELF']) )
            {
                $pathInfo = $_server['PHP_SELF'];
            }
            elseif ( isset($_server['REDIRECT_URL']) )
            {
                $pathInfo = $_server['REDIRECT_URL'];
            }
            else
            {
                $pathInfo = false;
            }
        }

        BC::loader()->request->server->set('PATH_INFO', $pathInfo);
        $pathInfo && $pathInfoArray = explode('/', trim($pathInfo, '/'));
		
        if (!isset($pathInfoArray[0]))
        {
            $_routeConfig = BC::config('route');
            $pathInfoArray = array($_routeConfig['m'],
                                   $_routeConfig['c'],
                                   $_routeConfig['a']
            );
        }

        self::$module = strtolower($pathInfoArray[0]);
        self::$controller = isset($pathInfoArray[1]) ? strtolower($pathInfoArray[1]) : 'index';
        self::$action = isset($pathInfoArray[2]) ? strtolower($pathInfoArray[2]) : 'index';

        return $pathInfoArray;
    }

    /**
     * 获取系统运行执行时间
     * @static
     * @return string
     */
    public static function getRunTime()
    {
        static $runTime;
        if ($runTime === null)
            $runTime =  microtime( TRUE ) - START_TIME;

        return $runTime;
    }

    /**
	 * 自动注册类库
     * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
     * @static
     * @param $className
     * @return void
     */
    public static function autoLoad($className)
	{
        $pathsString = '';
        foreach (self::$includePath as $path)
        {
            $pathsString .= PATH_SEPARATOR. $path;
        }

        set_include_path(get_include_path(). $pathsString);
        
		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';

		if($lastNsPos = strripos($className, '\\'))
		{
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DS, $namespace) . DS;
		}

		$fileName .= str_replace('_', DS, $className) . '.class.php';

		require_once($fileName);
	}
	
	/**
	 * 自动注册类库 - 来自配置项
     * @static
     * @return void|boolean
     */
	public static function autoLoadFromConfig()
	{
		$loadFiles = BC::config('autoload/global/library');
		
		if (!is_array($loadFiles) || !count($loadFiles)) return false;
		
		foreach ($loadFiles as $libraryPath)
		{
			BC::loader()->library($libraryPath, false);
		}

        return true;
	}
}