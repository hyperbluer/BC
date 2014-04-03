<?php
/**
 * Filter.php 过滤核心类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-24
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Filter
{
    /**
     * 请求过滤处理
     *
     * @static
     * @return void
     */
    public static function request()
    {
        //register_globals选项为on时，去掉自动注册变量
        if (ini_get('register_globals')) {

            $globals = array($_GET, $_POST, $_REQUEST, $_COOKIE, $_SESSION, $_SERVER, $_ENV, $_FILES);

            foreach ($globals as $global)
            {
                foreach(array_keys($global) as $key)
                {
                    unset($$key);
                }
            }
        }

        //过滤请求特殊字符及转义
        isset($_GET) && $_GET = self::sacarXss(self::escape($_GET));
		isset($_POST) && $_POST = self::escape($_POST);
		isset($_REQUEST) && $_REQUEST = self::escape($_REQUEST);
		isset($_COOKIE) && $_COOKIE = self::escape($_COOKIE);
		isset($_SESSION) && $_SESSION = self::escape($_SESSION);
		isset($_FILES) && $_FILES = self::escape($_FILES);
		isset($_SERVER) && $_SERVER = self::escape($_SERVER);

        //兼容windows IIS
        self::compatibleIIS();
    }

    /**
     * 过滤字符串和字符串数组，防止被挂马和sql注入
     *
     * @static
     * @param $data 待过滤的字符串或数组
     * @param bool $force 是否强制转义，为true则忽略get_magic_quotes_gpc
     * @return array|string
     */
    public static function escape($data, $force = false)
    {
        if(is_string($data))
        {
            $data = trim(htmlspecialchars($data));//防止被挂马，跨站攻击
            if(($force == true)||(!get_magic_quotes_gpc()))
            {
               $data = addslashes($data);//防止sql注入
            }
            return  $data;
        }
        elseif(is_array($data))//如果是数组采用递归过滤
        {
            foreach($data as $key => $value)
            {
                 $data[$key] = self::escape($value,$force);
            }
            return $data;
        }
        else
        {
            return $data;
        }
    }

    /**
     * 还原已转义的字符串或数组
     *
     * @static
     * @param $data
     * @return array|string
     */
    public static function unEscape($data)
    {
        if(is_string($data))
        {
            return stripslashes($data);
        }
        elseif(is_array($data))//如果是数组采用递归过滤
        {
            foreach($data as $key => $value)
            {
                 $data[$key] = self::unEscape($value);
            }
            return $data;
        }
        else
        {
            return $data;
        }
    }

    /**
     * html代码输入过滤
     *
     * @static
     * @param $string
     * @return mixed|string
     */
    public static function htmlEscape($string)
    {
        $search = array(
            "'<script[^>]*?>.*?</script>'si",
            "'<iframe[^>]*?>.*?</iframe>'si",
            "'<head[^>]*?>.*?</head>'is",
            "'<title[^>]*?>.*?</title>'is",
            "'<meta[^>]*?>'is",
            "'<link[^>]*?>'is",
        );
        $string = @preg_replace ($search, '', $string);
        $string = htmlspecialchars($string);
        if(!get_magic_quotes_gpc())
        {
            $string = addslashes($string);
        }

        return $string;
    }

    /**
     * html代码输出
     *
     * @static
     * @param $string
     * @return string
     */
    public static function htmlUnEscape($string)
    {
        if(function_exists('htmlspecialchars_decode'))
            $string = htmlspecialchars_decode($string);
        else
            $string = html_entity_decode($string);

        $string = stripslashes($string);
        return $string;
    }

    /**
     * 去掉字符串或数组元素首尾空格
     *
     * @static
     * @param $data
     * @return string
     */
    public static function trim($data)
	{
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				$string[$key] = self::trim($value);
			}
		}else{
			return trim($data);
		}
		return $data;
	}

    /**
     * 兼容IIS，重置环境变量
     *
     * @static
     * @return void
     */
    public static function compatibleIIS()
    {
        if (!isset($_SERVER['DOCUMENT_ROOT']))
        {
            if (isset($_SERVER['SCRIPT_FILENAME']))
            {
                $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
            }
        }

        if (!isset($_SERVER['DOCUMENT_ROOT']))
        {
            if (isset($_SERVER['PATH_TRANSLATED']))
            {
                $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
            }
        }

        if (!isset($_SERVER['REQUEST_URI']))
        {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

            if (isset($_SERVER['QUERY_STRING']))
            {
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
    }

    /**
     * XXS跨站漏洞过滤
     *
     * @static
     * @param $val
     * @return mixed
     */
    public static function sacarXss($val)
    {

		$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++)
        {
			$val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
			$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
		}
		$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'blink', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound');
		$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
		$found = true;
		while ($found == true)
        {
			$val_before = $val;
			for ($i = 0; $i < sizeof($ra); $i++)
            {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++)
                {
					if ($j > 0)
                    {
						$pattern .= '(';
						$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
						$pattern .= '|(&#0{0,8}([9][10][13]);?)?';
						$pattern .= ')?';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
				$val = preg_replace($pattern, $replacement, $val);
				if ($val_before == $val)
                {
					$found = false;
				}
			}
		}
        
		return $val;
	}
}