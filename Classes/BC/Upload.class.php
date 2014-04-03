<?php
/**
 * Upload.class.php 上传类
 *
 * 支持批量上传
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-26
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Upload
{
	public $uploadFiles = array(); //用户上传的文件
    public $config = array();
	public $saveFilePath; //存放用户上传文件的路径
	public $maxFileSize; //文件最大尺寸
	public $allowType = array(); //默认允许用户上传的文件类型
	public $saveInfo = array(); //返回一组有用信息，用于提示用户。

    public $error; //记录最后一次出错信息

	public function __construct()
    {
        $this->config = BC::config('upload');
        $this->saveFilePath = $this->config['saveFilePath'] ? $this->config['saveFilePath'] : APP_UPLOAD_PATH;
	    $this->maxFileSize = $this->config['maxFileSize'] ? $this->config['maxFileSize'] : 0;
	    $this->allowType = is_array($this->config['allowType']) ? $this->config['allowType'] : array();
    }

    /**
     * 存储用户上传文件，检验合法性通过后，存储至指定位置。
     *
     * @return int 值为0时上传失败，非0表示上传成功的个数。
     */
	public function upload()
    {
		if (!count($this->uploadFiles['name']))
            return 0;

		foreach ($this->uploadFiles['name'] as $i => $value)
        {
		    //如果当前文件上传功能，则执行下一步。
			if ($this->uploadFiles['error'][$i] == 0)
            {
				//取当前文件名、临时文件名、大小、扩展名，后面将用到。
				$filename = $this->uploadFiles['name'][$i];
				$tmpName = Filter::unEscape($this->uploadFiles['tmp_name'][$i]);
				$size = $this->uploadFiles['size'][$i];
				$mimeType = $this->uploadFiles['type'][$i];
				$ext = File::getExt($this->uploadFiles['name'][$i]);

				//检测当前上传文件大小是否合法。
				if (!$this->checkSize($size))
                {
					$this->error = "文件太大. 文件名: ".$filename;
					$this->halt($this->error);
					continue;
				}

				//检测当前上传文件扩展名是否合法。
				if (!$this->checkExt($ext))
                {
					$this->error = "未知文件格式: .".$ext." 文件名: ".$filename;
					$this->halt($this->error);
					continue;
				}

				//检测当前上传文件是否非法提交。
				if(!is_uploaded_file($tmpName))
                {
					$this->error = "非法提交. 文件名: ".$filename;
					$this->halt($this->error);
					continue;
				}
                
				//移动文件后，重命名文件用。
				$baseName = File::getBaseName($filename, ".".$ext);
				//移动后的文件名
				$newFilename = $baseName."-".time().".".$ext;
				//组合新文件名再存到指定目录下，格式：存储路径 + 文件名 + 时间 + 扩展名
				$finalFilePath = $this->saveFilePath.$newFilename;

				if(!move_uploaded_file($tmpName, $finalFilePath))
                {
					$this->error = $this->uploadFiles['error'][$i];
					$this->halt($this->error);
					continue;
				}
				//存储当前文件的有关信息，以便其它程序调用。
				$this->saveInfo[$i] = array(
                    "filename" => $filename,
                    "ext" => $ext,
                    "mimeType" => $mimeType,
                    "size" => $size,
                    "newFilename" => $newFilename,
                    "filePath" => $finalFilePath
                );
			}
		}

		return count($this->saveInfo); //返回上传成功的文件数目
	}

	/*
     * 返回一些有用的信息，以便用于其它地方。
	 *
     * @return Array 返回最终保存的路径
     */
	public function getSaveInfo()
    {
		return $this->saveInfo;
	}

	/*
	 * 检测用户提交文件大小是否合法
	 *
	 * @param integer $size 用户上传文件的大小
	 * @return bool 如果为true说明大小合法，反之不合法
	 */
	protected function checkSize($size)
    {
		if ($size > $this->maxFileSize)
        {
			return false;
		}
        else
        {
			return true;
		}
	}

	/*
     * 检测用户提交文件类型是否合法
	 *
	 * @return bool 如果为true说明类型合法，反之不合法
	 */
	protected function checkExt($extension)
    {
		foreach ($this->allowType as $type)
        {
		    if (strcasecmp($extension , $type) == 0)
			    return true;
		}

		return false;
	}

	/*
	 * 显示出错信息
	 *
     * @param $msg    要显示的出错信息
     */
	protected function halt($msg)
    {
		printf("<b><UploadFile Error:></b> %s <br>\n", $msg);
	}
	
}
