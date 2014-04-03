<?php
/**
 * HandlerInterface.class.php Http请求接口类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-28
 */
 
defined('IN_BC') or exit("Access Denied!");

interface BC_Http_HandlerInterface
{
    /**
     * 发送http请求
     *
     * @throws BC_Exception
     * @param Http_Request $request 请求头实例化对象
     * @return mixed
     */
    public function request(Http_Request $request);

    /**
     * 获取相应信息
     *
     * @return string|array
     */
    public function getResponse();

    /**
     * 获取请求错误信息
     *
     * @return string|array
     */
    public function getError();
    
}
