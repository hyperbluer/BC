<?php
/**
 * Curl.class.php Curl通讯类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-28
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Http_Handler_Curl implements Http_HandlerInterface
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
     * 采取curl方式发送http请求
     *
     * @see Http_HandlerInterface::request()
     * @throws BC_Exception
     * @param Http_Request $request 请求头实例化对象
     * @return mixed
     */
    public function request(Http_Request $request)
    {
        if (!Http::isSupportCurl())
        {
            throw new BC_Exception('Curl operation is not supported, need open curl extension!');
        }
        $handle = curl_init();

        $opts = array(
            CURLOPT_URL => $request->getUrl(),
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT => $request->getTimeout()
        );

        //获取自定义请求头
        $header = $request->getHeader();
        if (is_array($header) && count($header))
        {
            $formatHeader = array();
            foreach($header as $key => $value)
			{
				$formatHeader[] = $key . ': ' . $value;
			}
            
            $opts[CURLOPT_HEADER] = false;
            $opts[CURLOPT_HTTPHEADER] = array_values($formatHeader);
        }

        //获取请求数据
        $data = $request->getData();
		if(!empty($data))
		{
			$opts[CURLOPT_POSTFIELDS] = $data;
		}

        //判断是否应用cookie，一般用于模拟登陆
        $cookie = $request->getCookie();
        if(!empty($cookie))
        {
            $opts[CURLOPT_COOKIEFILE] = $cookie;
            $opts[CURLOPT_COOKIEJAR] = $cookie;
        }

        curl_setopt_array($handle, $opts);

        $response = curl_exec($handle);

        if(!curl_errno($handle))
		{
			$this->error = false;
			$this->request   = curl_getinfo($handle, CURLINFO_HEADER_OUT);
			$this->response  = $response;
		}
		else
		{
			$this->error = curl_error($handle);
			$this->request   = false;
			$this->response  = false;
		}

		curl_close($handle);

        return $response;
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