<?php
/**
 * Http.class.php Http通讯类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Http
{
    /**
     * http请求处理实例化对象，目前提供curl及fsocket
     *
     * @var
     */
    private $handler;

    /**
     * 构造函数
     * 实例化http请求处理对象
     *
     * @throws BC_Exception
     * @param string $handlerName
     */
    public function __construct($handlerName = '')
    {
        $handlerName = $handlerName ? $handlerName :
                (self::isSupportCurl() ? 'curl' : 'fsocket');

        if ( null === $this->handler )
        {
            $handlerName = 'Http_Handler_' . ucfirst($handlerName);

            if (!class_exists($handlerName))
            {
                throw new BC_Exception('Http Handler:' . $handlerName . ' haven\'t found!');
            }
            
            $this->handler = new $handlerName();
        }
    }

    /**
     * 发送http请求，返回内容
     *
     * @param Http_Request $request
     * @return string|array
     */
    public function request(Http_Request $request)
    {
        $response = $this->handler->request($request);

        return $response;
    }

    /**
     * 判断系统是否开启curl扩展
     *
     * @static
     * @return boolean
     */
    public static function isSupportCurl()
    {
        return function_exists('curl_init');
    }

   /**
    * HTTP请求返回状态码表
    *
    * @static
    * @param $code
    * @return string
    */
    public static function codeMap($code)
    {
        $maps   = array(

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'

        );

        return isset($maps[$code]) ? $maps[$code] : '';
    }
}