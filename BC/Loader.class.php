<?php
/**
 * Loader.php 装载核心类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */

defined('IN_BC') or exit("Access Denied!");

final class BC_Loader
{
    /**
     * 注册者实例化对象
     *
     * @var \Registry
     */
	protected $registry;

    /**
     * 构造函数
     *
     * @param BC_Registry $registry    注册者实例化对象
     */
	public function __construct(BC_Registry $registry)
    {
		$this->registry = $registry;
	}

    /**
     * 魔术方法，或者已注册的key
     *
     * @param $key
     * @return null
     */
	public function __get($key)
    {
		return $this->registry->get($key);
	}

    /**
     * 魔术方法，注册key
     *
     * @param $key
     * @param $value
     * @return void
     */
	public function __set($key, $value)
    {
		$this->registry->set($key, $value);
	}

    /**
     * 加载model
     *
     * @throws BC_Exception
     * @param string $modelName
	 * @param boolean $init
     * @param string $appPath
     * @return mixed
     */
	public function model($modelName = '', $init = true, $appPath = '')
    {
        if (stripos($modelName, '/') !== false)
        {
            $modelArray = explode('/', $modelName);
            $module = $modelArray[0];
            $modelName = $modelArray[1];
        }
        else
        {
            $module = strtolower(BC::$module);
            $modelName = $modelName ? $modelName : strtolower(BC::$module);
        }

        $appPath  = File::isDir($appPath) ? $appPath : APP_PATH;
	    $modelFile = $appPath.'modules'.DS.$module.DS.'models'.DS.$modelName.'.php';
	    if (file_exists($modelFile))
        {
            include_once $modelFile;
            $className = 'Model_'.ucfirst($modelName);
			if ($init) 
			{
                if (class_exists($className, false))
                {
                    $model = new $className;
				    return $model;
                }
                else
                {
                    throw new BC_Exception('Model class ['.$className.'] does not exist.');
                }

			}
	    }
        else
        {
            throw new BC_Exception('Model file ['.$modelFile.']does not exist.');
        }
	}

    /**
     * 加载自定义类库
     *
     * @throws BC_Exception
     * @param $libraryPath
     * @param bool $init
     * @param string $appPath
     * @return mixed
     */
	public function library($libraryPath, $init = true, $appPath = '')
    {
        $appPath  = File::isDir($appPath) ? $appPath : APP_PATH;
	    $libraryFile = $appPath.'libraries'.DS.$libraryPath.'.php';
	    if (file_exists($libraryFile))
        {
			$library = str_replace(' ', '_', ucwords(str_replace('/', ' ', $libraryPath))); 
            $className = 'App_'.$library;
	        include_once $libraryFile;
			if ($init) 
			{
                if (class_exists($className, false))
                {
				    $library = new $className;
				    return $library;
                }
                else
                {
                    throw new BC_Exception('Library class ['.$className.'] does not exist.');
                }
			}
	    }
        else
        {
            throw new BC_Exception('Library file ['.$libraryFile.'] does not exist.');
        }
	}
}