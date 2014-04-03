<?php
/**
 * Fsocket.class.php Fsocket通讯类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-28
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Http_Handler_Fsocket implements Http_HandlerInterface
{
    /**
     * 请求头信息
     *
     * @var
     */
    private $request;

    /**
     * 响应信息
     *
     * @var
     */
    private $response;

    /**
     * 请求返回错误信息
     *
     * @var
     */
    private $error;

    /**
     * 采取fsocket方式发送http请求
     *
     * @see Http_HandlerInterface::request()
     * @throws BC_Exception
     * @param Http_Request $request 请求头实例化对象
     * @return mixed
     */
    public function request(Http_Request $request)
    {
        $handle = fsockopen($request->getHost(), $request->getPort(), $errno, $errstr);

        if($handle !== false)
		{
            //设置超时时间
            $timeout = $request->getTimeout();
			if(!empty($timeout))
			{
				stream_set_timeout($handle, $timeout);
			}
            
            //格式化请求头
            $header = $request->getHeader();
            //请求头第一行发送标识 'GET / HTTP/1.0'
            $formatHeader = $request->getMethod() . ' ' . $request->getPath() . ' ' . $request->getScheme()."\r\n";
            if (is_array($header) && count($header))
            {
                foreach($header as $key => $value)
                {
                    $formatHeader .= $key . ': ' . $value."\r\n";
                }
            }

            if(!fwrite($handle, $formatHeader))
			{
				throw new BC_Exception('Could not write to stream');
			}

            //获取响应的数据
            $response = '';
            while (!feof($handle))
            {
                if (($header = @fgets($handle)) && ($header == "\r\n" || $header == "\n"))
                {
                    break;
                }
            }
            while (!feof($handle))
            {
                $data = fread($handle, 118192);
                $response .= $data;
            }

            //关闭连接
            fclose($handle);

            $this->error = false;
			$this->request   = $request;
			$this->response  = $response;
        }
        else
        {
            $this->error = $errstr;
			$this->request   = false;
			$this->response  = false;

			return false;
        }

        return $this->response;
    }

    /**
     * 获取请求头信息
     *
     * @return
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 获取相应信息
     *
     * @see Http_HandlerInterface::getResponse()
     * @return
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取请求错误信息
     *
     * @see Http_HandlerInterface::getError()
     * @return
     */
    public function getError()
    {
        return $this->error;
    }
}