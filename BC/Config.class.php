<?php
/**
 * Config.php 配置核心类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */

defined('IN_BC') or exit("Access Denied!");

final class BC_Config
{
    /**
     * 配置数组集合，包含默认及项目内部自定义配置项
     *
     * @var array
     */
    private $data = array();

    /**
     * 获取单一配置项
     *
     * @param $key
     * @return null
     */
  	public function get($key)
    {
    	return (isset($this->data[$key]) ? $this->data[$key] : null);
  	}

    /**
     * 自定义配置项
     *
     * @param $key
     * @param $value
     * @return void
     */
	public function set($key, $value)
    {
    	$this->data[$key] = $value;
  	}

    /**
     * 判断单一配置项是否存在
     *
     * @param $key
     * @return bool
     */
	public function has($key)
    {
    	return isset($this->data[$key]);
  	}

    /**
     * 获取所有配置项
     *
     * @return array
     */
    public function getAll()
    {
    	return $this->data;
  	}

    /**
     * 装载配置文件
     *
     * @param array $files  配置文件名
     * @return void
     */
  	public function load($files = array())
    {
        foreach ($files as $filename)
        {
            $file = APP_CFG_PATH . $filename . '.php';

            if (file_exists($file))
            {
                $_config = array();

                require($file);

                $this->data = array_merge($this->data, $_config);
            }
            else
            {
                trigger_error('Error: Could not load config ' . $filename . '! [file path: '.$file.']');
                exit();
            }
        }
  	}
}