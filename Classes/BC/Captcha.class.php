<?php
/**
 * Captcha.class.php 图片验证码生成类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-25
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Captcha
{
	public $border = 0; //是否要边框
	public $length = 4; //验证码位数
	public $width = 60; //图片宽度
	public $height = 30; //图片高度
    public $setSession = 0;
    protected $code;
    protected $image;

    /**
     * 构造函数
     */
	function __construct()
    {
        $this->setCode();
	}

    /**
     * 获取验证码
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function setCode()
    {
        $this->code = substr(sha1(mt_rand()), 17, $this->length);
    }

    /**
     * 创建生成的图片
     *
     * @return bool
     */
	function create()
    {
		$this->image = ImageCreate($this->width, $this->height); //创建图片
        $this->Rectangle();
        $this->randCode();
        $this->SetPixel();
        $this->setSession();

        BC::loader()->response->addHeader('Content-type:image/gif');
		return Imagegif($this->image);
	}
	
    /**
     * 绘制基本框架
     *
     * @return void
     */
    function rectangle()
    {
    	srand((double)microtime()*1000000); //初始化随机数种子
        $bgColor = ImageColorAllocate($this->image, 255, 255, 255); //设置背景颜色
        ImageFill($this->image, 0, 0, $bgColor); //填充背景色
        if($this->border)
        {
            $black = ImageColorAllocate($this->image, 0, 0, 0); //设置边框颜色
            ImageRectangle($this->image, 0, 0, $this->width-1, $this->height-1, $black);//绘制边框
        }
    }

    /**
     * 产生随机字符
     *
     * @return void
     */
    function randCode()
    {
        $blackColor = imagecolorallocate($this->image, 0, 0, 0);
        imagestring($this->image, 10, intval(($this->width - (strlen($this->code) * 9)) / 2),  intval(($this->height - 15) / 2), $this->code, $blackColor);
    }

    /**
     * 添加干扰
     *
     * @return void
     */
    function SetPixel()
    {
    	//绘背景干扰线
        for($i=0; $i<1; $i++)
        {
            $color1 = ImageColorAllocate($this->image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰线颜色
            ImageArc($this->image, mt_rand(-5,$this->width), mt_rand(-5,$this->height), mt_rand(20,300), mt_rand(20,200), 55, 44, $color1); //干扰线
        }
        //绘背景干扰点
        for($i=0; $i<$this->length*5; $i++)
        {
            $color2 = ImageColorAllocate($this->image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰点颜色
            ImageSetPixel($this->image, mt_rand(0,$this->width), mt_rand(0,$this->height), $color2); //干扰点
        }
    }

    /**
     * 把验证码字符串写入session
     *
     * @return void
     */
    function setSession()
    {
        $this->setSession && BC::loader()->session->set('captcha', $this->code);
    }

    /**
     * 析构函数 销毁图片
     */
    function __destruct()
    {
        $this->image && ImageDestroy($this->image);
    }
}