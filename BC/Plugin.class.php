<?php
/**
 * Plugin.class.php 插件类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-10
 */
defined('IN_BC') or exit("Access Denied!");

class BC_Plugin
{
    /**
     * Plugin实例对象
     *
     * @var null
     */
	protected static $instance = null;

    /**
     * 单例模式
     *
     * @static
     * @return null
     */
	public static function instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new BC_Plugin();
		}

		return self::$instance;
	}

    public function activate($plugin)
    {
		$config = array('status' => 1);
		return $this->saveConfig($plugin, $config);
    }

    public function deactivate($plugin)
    {
        $config = array('status' => 0);
		return $this->saveConfig($plugin, $config);
    }
	
    public function load()
    {
        $configs = $this->getConfigs();
		
		foreach ($configs as $k => $v)
		{
			if ($v['status'] != 1) continue;
			
			$pluginFile = APP_EXT_PATH.$k.DS.'plugin.php';
            File::isFile($pluginFile) && include_once $pluginFile;
		}
    }
	
	public function getConfigs()
	{
		static $configs = array();
		if (count($configs)) return $configs;
		
        $pluginList = File::getDirTree(APP_EXT_PATH, 0, array(), 1);
        foreach ($pluginList as $v)
        {
            $configFile = $v['dir'].'config.php';
            File::isFile($configFile) && $configs[$v['name']] = include $configFile;
        }

        return $configs;
	}
	
	public function saveConfig($plugin, $newConfig = array()){
        $configFile = APP_EXT_PATH.$plugin.DS.'config.php';

        if (File::isFile($configFile))
        {
            $config = include $configFile;
            $config = array_merge($config, $newConfig);
        }
        else
        {
            $config = $newConfig;
        }

        $fileClass = new File($configFile, 'w');
        $content = var_export($config, true);
        $content = "<?php \r\nreturn " . $content . ';';
	    if ($fileClass->write($content))
        {
            return true;
        }

        return false;
    }
}