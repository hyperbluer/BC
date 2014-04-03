<?php
/**
 *  Global.func.php 全局函数库
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-21
 */

if (!function_exists('__'))
{
    /**
     * 语言包
     *
     * @param $string
     * @param array|null $values
     * @return string
     */
    function __($string, array $values = NULL )
    {
        $string = BC::loader()->lang->get($string);
        return empty( $values ) ? $string : strtr( $string, $values );
    }
}

if (!function_exists('_url'))
{
    /**
     * 统一URL路径
     *
     * @param string $path  系统内部路径采取 module/controller/action
     * @param array $params 传递的参数键值对
     * @param bool $internal 是否为系统内部路径，若$path为外部路径，请设置为false
     * @return null|string
     */
    function _url($path = '', array $params = array(), $internal = true)
    {
        if (empty($path))
        {
            $url = Url::getCurrentUrl();
        }
        elseif ($internal === true)
        {
            if ($path == '#') return $path;
            
            $url = BC::config('root_path');
            $path = ltrim($path,'/');
        }
        else
        {
            $url = '';
        }

        if (($position = strpos($path, '?')) !== false)
        {
            $pathParamsString = substr($path, $position+1, strlen($path));
            parse_str($pathParamsString, $pathParams);
            $params = ArrayConvert::mergeArray($pathParams, $params);
            $url .= substr($path, 0, $position);
        }
        else
        {
            $url .= $path;
        }

        count($params) && $url .= '?'.http_build_query($params);

        return $url;
    }
}

if (!function_exists('_redirect'))
{
    /**
     * 系统程序代码路径跳转
     *
     * @param $url  内部路径请先采取_url函数处理
     * @return void
     */
    function _redirect($url)
    {
        //header('Location:'.$url);
        BC::loader()->response->addHeader('Location:'.$url);
        BC::loader()->response->sendHeaders();
        exit();
    }
}