<?php
/**
 * Validator.class.php 验证类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-24
 */

class BC_Validator
{
    /**
     * 检查用户名是否符合规定(2-20位字母数字汉字以及下划线)
     *
     * @static
     * @param   string   $username   要检查的用户名
     * @return   boolean
     */
    public static function username($username)
    {
        $pattern = "/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/";
        $strLen = strlen($username);
        if(self::badWord($username) || !preg_match($pattern, $username))
        {
            return false;
        }
        elseif ( 20 <= $strLen || $strLen < 2 )
        {
            return false;
        }
        
        return true;
    }

    /**
     * 检查密码长度是否符合规定(6-20位)
     *
     * @static
     * @param   string   $password
     * @return   boolean
     */
    public static function password($password)
    {
        $strLen = strlen($password);
        if($strLen >= 6 && $strLen <= 20) return true;
        return false;
    }

    /**
     * 验证输入的邮件地址是否合法
     *
     * @static
     * @param   string   $email   需要验证的邮件地址
     * @return   boolean
     */
    public static function email($email)
    {
        $pattern = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($email, '@') !== false && strpos($email, '.') !== false)
        {
            if (preg_match($pattern, $email)) return true;
            else return false;
        }
        else
        {
            return false;
        }
    }

    /**
     * 判断移动手机格式是否正确
     *
     * @static
     * @param   $phone
     * @return   boolean
     */
    public static function phone($phone)
    {
        $pattern = "/^1[0-9]{10}$/";
        return strlen($phone) == 11 && preg_match($pattern, $phone);
    }

    /**
     * 判断QQ格式是否正确
     *
     * @static
     * @param   $qq
     * @return   boolean
     */
    public static function qq($qq)
    {
        $pattern = "/^[1-9]\d{4,12}$/";
        return strlen($qq) > 4 && preg_match($pattern, $qq);
    }

    /*
     * 身份证号码验证
     *
     * @static
     * @param   $idCard
     * @param   $length
     * @return   boolean
     */
    public static function idCard($idCard, $length = 'both')
    {
        if(strlen($idCard) == 15 && $length == 'both')
        {
            //当$length不等于'both'时，15位号码无效
            $realIdCard = substr($idCard, 0, 6) . '19' . substr($idCard, 6); //为返回18位号码作准备。
            $pattern = "/^[\d]{8}((0[1-9])|(1[0-2]))((0[1-9])|([12][\d])|(3[01]))[\d]{3}$/";
        }
        elseif (strlen($idCard) == 18)
        {
            $realIdCard = substr($idCard, 0, 17);
            $pattern = "/^[\d]{6}((19[\d]{2})|(200[0-8]))((0[1-9])|(1[0-2]))((0[1-9])|([12][\d])|(3[01]))[\d]{3}[0-9xX]$/";
        }
        else
        {
            return false;
        }

        //验证身份证格式是否正确
        if (!preg_match($pattern, $idCard))
            return false;

        //验证是否是有效的身份证号
        $sum = 0;
        $algorithm = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
        foreach ($algorithm as $k => $v)
        {
            $sum += substr($realIdCard, $k, 1) * $v;
        }

        $lastNumber = 12 - $sum % 11;
        if ($lastNumber == 10)
            $lastNumber = 'x';
        elseif ($lastNumber == 12)
            $lastNumber = '1';
        elseif ($lastNumber == 11)
            $lastNumber = '0';

        /* ----------18位验证码计算完成------------- */
        if (strlen($idCard) == 18)
        {
            if (strtolower(substr($idCard, 17, 1)) != $lastNumber)
                return false;
        }
        
        return true;
    }

    /**
     * 检查是否含有系统限制的非法字符
     *
     * @static
     * @param   string   $string
     * @return   boolean
     */
    public static function badWord($string)
    {
        return false;
    }

    /**
     * 检查是否为一个合法的时间格式
     *
     * @static
     * @param   string   $time
     * @return   boolean
     */
    public static function time($time)
    {
        $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

        return preg_match($pattern, $time);
    }

    /**
     * IE浏览器判断
     *
     * @static
     * @return   boolean
     */
    public static function IE()
    {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

        if ((strpos($userAgent, 'opera') !== false) || (strpos($userAgent, 'konqueror') !== false))
            return false;
        if (strpos($userAgent, 'msie') !== false)
            return true;

        return false;
    }

    /**
     * 检查是否是引擎爬虫和机器人访问网站
     *
     * @static
     * @return   boolean
     */
    public static function robot()
    {
        static $isRobot = null;

        if(null === $isRobot)
        {
            $isRobot = false;
            $robotList = 'bot|spider|crawl|nutch|lycos|robozilla|slurp|search|seek|archive';
            if( isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/{$robotList}/i", $_SERVER['HTTP_USER_AGENT']) )
                $isRobot = true;
        }

        return $isRobot;

    }

    /**
     * 判断是否为手持设别的函数
     *
     * @static
     * @return   boolean
     */
    public static function mobile()
    {
        $devices = array(
            "operaMobi" => "Opera Mobi",
            "android" => "android",
            "blackberry" => "blackberry",
            "iphone" => "(iphone|ipod)",
            "opera" => "opera mini",
            "palm" => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
            "windows" => "windows ce; (iemobile|ppc|smartphone)",
            "generic" => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)"
        );

        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']))
        {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') > 0 || strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') > 0))
        {
            return true;
        } else {
            if (isset($_SERVER['HTTP_USER_AGENT']))
            {
                foreach ($devices as $device => $regexp)
                {
                    if (preg_match("/" . $regexp . "/i", $_SERVER['HTTP_USER_AGENT']))
                    {
                        return true;
                    }
                }
            }
        }
        return FALSE;
    }

    /**
     * 判断字符串是否为utf8编码，英文和半角字符返回ture
     *
     * @static
     * @param   $string
     * @return  boolean
     */
    public static function utf8($string)
    {
        return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E] # ASCII
            | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
            )*$%xs', $string);
    }

}