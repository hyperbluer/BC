<?php
/**
 * Controller.php 入口控制器
 *
 * 系统入口控制器,所有模块控制器继承此类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Controller {
    
	/**
	 * 构造函数
	 *
	 */
    public function __construct() {
        $this->loader = BC::loader();
        $this->tpl = $this->loader->tpl;
        $this->request = $this->loader->request;
        $this->response = $this->loader->response;
		$this->lang = $this->loader->lang;
		$this->session = $this->loader->session;
    }
	
	public function getRoute()
	{
		return strtolower(BC::$module.'/'.BC::$controller.'/'.BC::$action);
	}
}
