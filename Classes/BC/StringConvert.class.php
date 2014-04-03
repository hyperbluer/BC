<?php
/**
 * String.class.php 字符串处理类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-21
 */

defined('IN_BC') or exit("Access Denied!");

class BC_StringConvert
{
    /**
     * 截取字符串，主要针对非纯英文字符串
     *
     * @static
     * @param $string   待截取的字符串
     * @param $length   截取长度
     * @param int $start 开始截取字符位置，默认为0
     * @param string $charset 编码，默认为uft-8，可选utf-8/gb2312/gbk/big5
     * @param bool $dot 是否显示省略号,默认为false
     * @return string
     */
    public static function substr($string, $length, $start = 0, $charset = 'utf-8', $dot = false)
    {
        switch($charset)
        {
            case 'utf-8' :
                $charLen = 3;
                break;
            case 'UTF8':
                $charLen = 3;
                break;
            default:
                $charLen = 2;
        }
        //小于指定长度，直接返回
        if(strlen($string) <= ($length*$charLen))
        {
            return $string;
        }
        if(function_exists("mb_substr"))
        {
            $slice = mb_substr($string, $start, $length, $charset);
        }
        else if(function_exists('iconv_substr'))
        {
            $slice = iconv_substr($string,$start,$length,$charset);
        }
        else
        {
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            $pattern = isset($re[$charset]) ? $re[$charset] : $re['utf-8'];
            preg_match_all($pattern, $string, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        if($dot)
            return $slice."…";

        return $slice;
    }

    /**
     * 获取字符串长度，主要针对非纯英文字符串
     *
     * @static
     * @param $string
     * @param string $charset
     * @return int
     */
    public static function strlen($string, $charset = 'uft-8')
    {
        $len = strlen($string);
		$i = $count = 0;
		$charset = strtolower(substr($charset, 0, 3));
		while ($i < $len) {
			if (ord($string[$i]) <= 129)
				$i++;
			else
				switch ($charset) {
					case 'utf':
						$i += 3;
						break;
					default:
						$i += 2;
						break;
				}
			$count++;
		}
        
		return $count;
    }

    /**
     * 生成随机字符串
     *
     * @static
     * @param $length   随机字符串长度
     * @param string $chars  可惜，默认为0123456789
     * @return string
     */
    public static function random($length, $chars = '0123456789')
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 生成唯一值
     *
     * @static
     * @return string
     */
    public static function uniqid()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * 加密函数
     *
     * @static
     * @param $data 待解密的字符串
     * @param string $key   密钥
     * @param int $expire
     * @return string
     */
    public static function encode($data, $key='', $expire = 0)
    {
        $string=serialize($data);
        $ckey_length = 4;//note 随机密钥长度 取值 0-32;
        //note 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        //note 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        //note 当此值为 0 时，则不产生随机密钥
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = substr(md5(microtime()), -$ckey_length);

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string =  sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++)
        {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        return $keyc.str_replace('=', '', base64_encode($result));
    }

    /**
     * StringConvert::encode()之后的解密函数
     * 
     * @static
     * @param $string 待解密的字符串
     * @param string $key 密钥
     * @return mixed|string
     */
    public static function decode($string, $key='')
    {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = substr($string, 0, $ckey_length);

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string =  base64_decode(substr($string, $ckey_length));
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++)
        {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return unserialize(substr($result, 26));
        }
        else
        {
            return '';
        }
    }
}