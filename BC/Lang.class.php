<?php
/**
 * Lang.class.php 语言包类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-26
 */

defined('IN_BC') or exit("Access Denied!");

final class BC_Lang
{
    /**
     * 当前语言，默认为中文简体
     *
     * @var string
     */
	private $lang = 'zh_CN';

    /**
     * 加载在系统中的语言数据数组
     * 
     * @var array
     */
	private $data = array();
 
	public function __construct()
    {
        //TODO 根据请求头HTTP_ACCEPT_LANGUAGE返回对应语言包
	}

    /**
     * 设置或获取语言
     *
     * @param string $lang
     * @return string
     */
	public function lang($lang = '')
    {
        $lang && $this->lang = $lang;
        return $this->lang;
	}

    /**
     * 获取key对应的语言内容
     *
     * @param $string
     * @return string
     */
  	public function get($string)
    {
        $string = strtolower($string);
		if (($flag = strripos($string, '/')) !== false)
        {
            $key = substr($string, $flag+1, strlen($string));
            $fileName = str_replace('/', DS, (substr($string, 0, $flag)));
            $this->load($fileName);
		}
        else
        {
            $key = $string;
        }

   		return (isset($this->data[$key]) ? $this->data[$key] : $key);
  	}

    /**
     * 装载语言包文件
     *
     * @throws BC_Exception
     * @param $fileName
     * @return array
     */
	public function load($fileName)
    {
		$file = APP_LANG_PATH . $this->lang . DS . $fileName . '.php';
		if (file_exists($file))
        {
			$_ = array();
	  		
			include_once $file;
		
			$this->data = array_merge($this->data, $_);

			return $this->data;
		}
        else
        {
			throw new BC_Exception('Could not load language file ['.$this->lang. '/' . $fileName . ']!');
		}
  	}

}