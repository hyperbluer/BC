<?php
/**
 * Response.class.php 内容信息响应类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-20
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Response
{
    /**
     * 响应头信息
     *
     * @var array
     */
    private $headers = array();

    /**
     * 编码格式
     *
     * @var string
     */
    private $charset = 'utf-8';

    /**
     * 状态码
     *
     * @var string
     */
    private $statusCode = '';

    /**
     * 压缩级别
     *
     * @var int
     */
    private $level = 0;

    /**
     * 响应内容信息
     *
     * @var 
     */
    private $output;
    
    /**
	 * 获取响应头信息
	 *
	 * @return array
	 */
	public function getHeaders()
    {
		return $this->headers;
	}
    
    /**
     * 添加响应头信息
     * 
     * @param $header
     * @return void
     */
    public function addHeader($header)
    {
		$this->headers[] = $header;
	}
    
    /**
	 * 清除响应头信息
	 *
	 * @return void
	 */
	public function clearHeaders()
    {
		$this->headers = array();
	}

    /**
	 * 设置响应头状态码
	 *
	 * @param int $statusCode 响应状态码
	 * @return void
	 */
	public function setStatusCode($statusCode)
    {
		$statusCode = intval($statusCode);
		if ($statusCode < 100 || $statusCode > 505) return;
		$this->statusCode = (int) $statusCode;
	}

    /**
	 * 获得输出的编码方式
	 *
	 * @return string
	 */
    public function getCharset()
    {
		return $this->charset;
	}
    
    /**
	 * 设置输出的编码方式
	 *
	 * @param string $charset 编码方式
	 * @return void
	 */
	public function setCharset($charset)
    {
		$this->charset = $charset;
	}

    /**
     * 设置压缩级别
     *
     * @param $level
     * @return void
     */
    public function setCompression($level)
    {
		$this->level = $level;
	}

    /**
     * 设置响应内容信息
     *
     * @param $output
     * @return void
     */
	public function setOutput($output)
    {
		$this->output = $output;
	}
    
    /**
	 * 是否已经发送了响应头部
	 *
	 * @param boolean $throw 是否抛出错误,默认为false：
	 * <ul>
	 * <li>true: 如果已经发送了头部则抛出异常信息</li>
	 * <li>false: 无论如何都不抛出异常信息</li>
	 * </ul>
	 * @return boolean 已经发送头部信息则返回true否则返回false
	 */
    public function isSendedHeader($throw = false)
    {
		$sended = headers_sent($file, $line);
		if ($throw && $sended) throw new BC_Exception(
			__CLASS__ . ' the headers are sent in file ' . $file . ' on line ' . $line);
		return $sended;
	}
    
    /**
	 * 发送响应头部信息
	 *
	 * @return void
	 */
    public function sendHeaders() {
		if ($this->isSendedHeader()) return;
		foreach ($this->headers as $header)
        {
			header($header, true);
		}
		if ($this->statusCode)
        {
			header('HTTP/1.x ' . $this->statusCode . ' ' . ucwords($this->codeMap($this->statusCode)));
			header('Status: ' . $this->statusCode . ' ' . ucwords($this->codeMap($this->statusCode)));
		}
	}

    /**
	 * 输出响应信息
	 *
	 * @return void
	 */
    public function output()
    {
		if ($this->level)
        {
			$ouput = $this->compress($this->output, $this->level);
		}
        else
        {
			$ouput = $this->output;
		}

        $this->sendHeaders();

        echo $ouput;
	}

    private function compress($data, $level = 0)
    {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false))
        {
			$encoding = 'gzip';
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false))
        {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding))
        {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
        {
			return $data;
		}

		if (headers_sent())
        {
			return $data;
		}

		if (connection_status())
        {
			return $data;
		}

		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}

    /**
     * 状态码对照表
     * 
     * @param $statusCode
     * @return string
     */
    public function codeMap($statusCode)
    {
		$maps = array(
			505 => 'http version not supported',
			504 => 'gateway timeout',
			503 => 'service unavailable',
			503 => 'bad gateway',
			502 => 'bad gateway',
			501 => 'not implemented',
			500 => 'internal server error',
			417 => 'expectation failed',
			416 => 'requested range not satisfiable',
			415 => 'unsupported media type',
			414 => 'request uri too long',
			413 => 'request entity too large',
			412 => 'precondition failed',
			411 => 'length required',
			410 => 'gone',
			409 => 'conflict',
			408 => 'request timeout',
			407 => 'proxy authentication required',
			406 => 'not acceptable',
			405 => 'method not allowed',
			404 => 'not found',
			403 => 'forbidden',
			402 => 'payment required',
			401 => 'unauthorized',
			400 => 'bad request',
			300 => 'multiple choices',
			301 => 'moved permanently',
			302 => 'moved temporarily',
			302 => 'found',
			303 => 'see other',
			304 => 'not modified',
			305 => 'use proxy',
			307 => 'temporary redirect',
			100 => 'continue',
			101 => 'witching protocols',
			200 => 'ok',
			201 => 'created',
			202 => 'accepted',
			203 => 'non authoritative information',
			204 => 'no content',
			205 => 'reset content',
			206 => 'partial content'
        );
        
		return isset($maps[$statusCode]) ? $maps[$statusCode] : '';
	}
}