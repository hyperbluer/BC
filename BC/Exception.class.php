<?php
/**
 * Exception.php 异常处理核心类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-20
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Exception extends Exception
{
    /**
     * 错误代码
     *
     * @var int
     */
    private $errNo;

    /**
     * 错误信息
     *
     * @var string
     */
    private $errStr;

    /**
     * 出错文件
     *
     * @var string
     */
    private $errFile;
    
    /**
     * 出错文件代码行数
     *
     * @var int
     */
    private $errLine;

    /**
     * 追踪信息
     *
     * @var array
     */
    private $trace;

    /**
     * 自定义异常处理
     *
     * @param $message
     * @param int $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct();
        $this->errNo = $code ? $code :$this->getCode();
        $this->errStr = $message ? $message : $this->getMessage();
        $this->errFile =$this->getFile();
        $this->errLine = $this->getLine();
        $this->trace = $this->getTrace();
        return BC_Exception::errorHandler($this->errNo, $this->errStr, $this->errFile, $this->errLine, '', $this->trace);
    }

    /**
     * 自定义错误输出句柄
     *
     * @static
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $errContext
     * @param null $trace
     * @return bool
     */
    public static function errorHandler($errNo, $errStr, $errFile, $errLine, $errContext, $trace = NULL)
	{
        //记录日志
        if (BC::config('open_log'))
        {
            $message = Date::format().'|'.$errNo.'|'. $errStr.'|'.$errFile.'|'.$errLine."\n";
            Log::write($message);
        }
		
		$isError = false;
		switch($errNo)
		{
			case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
			case 404:
                $isError = true;
                break;
        }
		//若未报出致命错误，则页面正常显示
		if ($isError === false && !BC::config('open_debug'))
		{
			return true;
		}

		ob_clean();
		
		//debug模式下输出错误信息
		if (BC::config('open_debug'))
		{
			$traceInfo = '';
			if(!$trace && function_exists('debug_backtrace'))
			{
				$trace = debug_backtrace();
			}
			$time = date("Y-m-d H:i:s", BC_SYS_TIME);
			if (is_array($trace) && count($trace))
			{
				$t_default = array('file' => '', 'line' => '', 'class' => '', 'type' => '', 'function' => '', 'args' => '');
				foreach($trace as $t)
				{
					$t = array_merge($t_default, $t);
					$traceInfo .= '['.$time.'] ' . $t['file'] . ' (' . $t['line'] . ') ';
					$traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
					$traceInfo .= ")\n";
				}
			}

			$e = array(
				'errNo' => $errNo,
				'errStr' => $errStr,
				'errFile' => $errFile,
				'errLine' => $errLine,
				//'trace' => $trace,
				'traceInfo' => $traceInfo,
				'errCodeTip' => BC_Exception::errorCodeMap($errNo)
			);
			
			if (File::isFile(BC_ERROR_PAGE_PATH.'error.php'))
			{
				BC::loader()->tpl->suffix = 'php';
				BC::loader()->tpl->clean();
				BC::loader()->tpl->assign('e', $e);
				BC::loader()->tpl->display('error', BC_ERROR_PAGE_PATH);
				exit();
			}
		}

		$errPage = $errNo == '404' ? '404' : '500';
		if (File::isFile(BC_ERROR_PAGE_PATH.$errPage.'.php'))
		{
            BC::loader()->tpl->suffix = 'php';
            BC::loader()->tpl->clean();
			BC::loader()->tpl->display($errPage, BC_ERROR_PAGE_PATH);
		}
		else
		{
			BC::loader()->response->setStatusCode('404');
			BC::loader()->response->setOutput('404 Not Found.');
			BC::loader()->response->output();
		}
        exit();
	}

    /**
     * 错误代码表
     *
     * @static
     * @param $code
     * @return string
     */
    public static function errorCodeMap($code)
    {
        $maps = array(
            1     => '致命运行时错误(E_ERROR)',
			2     => '运行时警告(E_WARNING)',
			4     => '编译语法解析错误(E_PARSE)',
			8     => '运行时提示(E_NOTICE)',
			16    => '启动时致命错误(E_CORE_ERROR)',
			32    => '启动时警告(E_CORE_WARNING)',
			64    => '编译时错误(E_COMPILE_ERROR)',
			128   => '编译时警告(E_COMPILE_WARNING)',
			256   => '致命错误(E_USER_ERROR)',
			512   => '自定义警告(E_USER_WARNING)',
			1024  => '自定义提示(E_USER_NOTICE)',
			2048  => '代码修改建议(E_STRICT)',
            4096  => '可捕捉致命错误(E_RECOVERABLE_ERROR)',
            8192  => '运行时提示(E_DEPRECATED)',
            16384 => '自定义提示(E_USER_DEPRECATED)',
            30719 => '系统错误(E_ALL)'
		);

        return isset($maps[$code]) ? $maps[$code] : '系统错误';
    }
}