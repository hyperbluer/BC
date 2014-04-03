<?php
/**
 * Template.class.php
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-19
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Template
{
    public $suffix = null;
	
	public $tplPath;
    /**
     * 模板临时注册的变量键值对
     *
     * @var array
     */
    protected $vars = array();

    /**
     * 模板替换标签的变量键值对
     * 
     * @var array
     */
    protected $replace = array();

    /**
     * 构造函数
     */
    public function __construct()
    {
	}

    /**
     * 注册模板变量
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function assign($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * 替换模板标签
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function replace($key, $value)
    {
        $this->replace[$key] = $value;
    }

    /**
     * 清除所有已注册的变量和替换
     *
     * @return void
     */
    public function clean()
    {
        $this->vars = array();
        $this->replace = array();
    }

    /**
     * 显示模板内容
     *
     * @param $tplName
     * @param string $tplPath
     * @return void
     */
    public function display($tplName, $tplPath = '')
    {
        echo $this->fetch($tplName, $tplPath);
    }

    /**
     * 解析模板
     *
     * @throws BC_Exception
     * @param $tplName
     * @param string $tplPath
     * @return mixed|string
     */
	public function fetch($tplName, $tplPath = '')
    {
        $___tpl___ = $this->getFilePath($tplName, $tplPath);

		if (file_exists($___tpl___))
        {

			extract($this->vars);

      		ob_start();

	  		include($___tpl___);

	  		$content = ob_get_contents();

      		ob_end_clean();

            if (is_array($this->replace) && count($this->replace) == 2)
            {
                return str_replace(array_keys($this->replace), array_values($this->replace), $content);
            }
            else
            {
                return $content;
            }
    	}
        else
        {
			throw new BC_Exception('Error: Could not load template ' . $___tpl___ . '!');
    	}	
    }

    /**
     * 获取模板路径
     *
     * @param $tplName
     * @param string $tplPath
     * @return string
     */
    public function getFilePath($tplName, $tplPath = '')
    {
        $this->suffix === null && $this->suffix = BC::config('tpl_suffix');
        empty($tplPath) && $tplPath = $this->tplPath;
        $filePath = $tplPath. str_replace('/', DS, $tplName). '.'.$this->suffix;

        return $filePath;
    }
}
