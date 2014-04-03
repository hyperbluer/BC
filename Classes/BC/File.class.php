<?php
/**
 * File.class.php 文件类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-25
 */
defined('IN_BC') or exit("Access Denied!");

class BC_File
{

    public $file;

    public $mode;

    public $handle;
    

    /**
     * 构造函数
     *
     * @throws BC_Exception
     * @param $file
     * @param string $mode
     */
    public function __construct($file, $mode = null)
    {
        $mode = $mode === null ? 'r' : $mode;

        if ($mode == 'r' && !self::isFile($file))
        {
            throw new BC_Exception('No such file：'.$file);
        }

        $handle = fopen($file, $mode);
		if(!$handle)
		{
			throw new BC_Exception('Could not open file ' . $file . ' in mode ' . $mode);
		}
		else
		{
            $this->file = $file;
            $this->mode = $mode;
			$this->handle = $handle;
		}
	}

    /**
     * 读取文件内容
     *
     * @param int $length 读取的最大字节数, 默认是1024字节
     * @return string
     */
    public function read($length = null)
    {
        $length === null && $length = self::getFileSize($this->file);

        if ($length <= 0) return NULL;
        return fread($this->handle, $length);
    }

    /**
     * 从文件指针中读取一行
     *
     * @param int|null $length  读取的字节数，默认一行。
     * @return string
     */
    public function gets($length = null)
	{
		if($length !== null)
		{
			return fgets($this->handle, $length);
		}
		else
		{
			return fgets($this->handle);
		}
	}

    /**
     * 写入文件
     *
     * @param $string   待写入文件的字符串
     * @param int|null $length  写入的最大字节数
     * @return int
     */
    public function write($string, $length = null)
	{
		if($length !== null)
		{
			return fwrite($this->handle, $string, $length);
		}
		else
		{
			return fwrite($this->handle, $string);
		}
	}

    /**
     * 删除文件
     *
     * @static
     * @param $file
     * @return bool
     */
    public static function delete($file)
    {
        if (file_exists($file))
            return unlink($file);
        else
            return false;
    }

    /**
     * 将文件内容截取到指定长度
     *
     * @param int $length
     * @return bool
     */
    public function truncate($length = 0)
    {
        if(ftruncate($this->handle, $length))
        {
            $this->seek($length);
            return true;
        }

        return false;
    }

    /**
     * 在打开文件中重新定位指针位置
     *
     * @param int $offset
     * @param int $whence SEEK_SET/SEEK_CUR/SEEK_END
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->handle, $offset, $whence);
    }

    /**
     * 检测是否已到达文件末尾 
     *
     * @return bool
     */
    public function eof()
	{
		return feof($this->handle);
	}

    /**
     * 关闭打开的文件
     *
     * @return bool
     */
    public function close()
	{
		return is_resource($this->handle) && fclose($this->handle);
	}

    /**
     * 获取文件大小
     *
     * @static
     * @param $file
     * @return int
     */
    public static function getFileSize($file)
    {
        return filesize($file);
    }

    /**
     * 重命名文件
     *
     * @static
     * @param $name
     * @param $newName
     * @return bool
     */
    public static function rename($name, $newName)
    {
        //if (!self::isFile($name) && !self::isDir($name)) return false;
        //if (!self::isFile($newName) && !self::isDir($newName)) return false;

        return rename($name, $newName);
    }

    /**
     * 判断是否是文件
     *
     * @static
     * @param $file
     * @return bool
     */
    public static function isFile($file)
    {
        return is_file($file);
    }

    /**
     * 判断是否是连接
     *
     * @static
     * @param $file
     * @return bool
     */
    public static function isLink($file)
    {
        return is_link($file);
    }

    /**
     * 判断是否是文件目录
     *
     * @static
     * @param $path
     * @return bool
     */
    public static function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * 判断指定文件名是否可读
     *
     * @static
     * @param $file
     * @return bool
     */
    public static function isRead($file)
    {
        return is_readable($file);
    }

    /**
     * 判断指定文件名是否可写
     *
     * @static
     * @param $file
     * @return bool
     */
    public static function isWrite($file)
    {
        return is_writable($file);
    }
	
	/**
	 * 创建目录
	 *
     * @static
	 * @param	string	$path	路径
	 * @param	int	    $mode	属性
	 * @return	bool	如果已经存在则返回true，否则为flase
	 */
	public static function mkDir($path, $mode = 0777)
    {
        $path = self::parsePath($path);
		if(is_dir($path)) return true;

		$temp = explode('/', $path);
		$currentDir = '';
		$max = count($temp) - 1;
		for($i = 0; $i < $max; $i++)
        {
			$currentDir .= $temp[$i].'/';
			if (@is_dir($currentDir)) continue;
			@mkdir($currentDir, $mode,true);
		}

		return is_dir($path);
	}
	
	/**
	 * 列出目录下所有文件
	 *
     * @static
	 * @param	string	$dir		路径
	 * @param	string	$ext		扩展名,多种采用|隔开，如html|htm
	 * @param	array	$list		增加的文件列表
     * @param   int     $returnLevel 返回目录层级
     * @param   int     $currentLevel 当前层级
	 * @return	array	所有满足条件的文件
	 */
	public static function listDir($dir, $ext = '', $list = array(), $returnLevel = 0, $currentLevel = 1)
    {
		$dir = self::parsePath($dir);
        if (!is_dir($dir)) return false;
        
		$files = glob($dir.'*');
		foreach($files as $v)
        {
            if ($ext && !preg_match("/\.($ext)/i", $v)) continue;

		    $list[] = $v;
            if (($returnLevel == 0 || $currentLevel < $returnLevel) && is_dir($v)) $list = self::listDir($v, $ext, $list, $returnLevel, $currentLevel+1);
		}
        
		return $list;
	}

	/**
	 * 删除目录及目录下面的所有文件
	 *
     * @static
	 * @param	string	$dir		路径
	 * @return	bool	如果成功则返回 TRUE，失败则返回 FALSE
	 */
	public static function rmDir($dir)
    {
		$dir = self::parsePath($dir);
		if (!is_dir($dir)) return false;
        
		$list = glob($dir.'*');
		foreach($list as $v)
        {
			is_dir($v) ? self::rmDir($v) : @unlink($v);
		}
		return @rmdir($dir);
	}
    
    /**
	 * 拷贝目录及下面所有文件
	 *
     * @static
	 * @param	string	$sourcePath	原路径
	 * @param	string	$targetPath		目标路径
	 * @return	string	如果目标路径不存在则返回false，否则为true
	 */
	public static function cpDir($sourcePath, $targetPath)
    {
		$sourcePath = self::parsePath($sourcePath);
		$targetPath = self::parsePath($targetPath);

		if (!is_dir($sourcePath)) return false;
		if (!is_dir($targetPath)) self::mkDir($targetPath);

		$list = glob($sourcePath.'*');
		if (!empty($list))
        {
			foreach($list as $v)
            {
				$path = $targetPath.basename($v);
				if(is_dir($v))
                {
					self::cpDir($v, $path);
				}
                else
                {
					copy($v, $path);
					@chmod($path, 0755);
				}
			}
		}
		return TRUE;
	}

	/**
	 * 转换目录下面的所有文件编码格式
	 *
     * @static
	 * @param	string	$in_charset		原字符集
	 * @param	string	$out_charset	目标字符集
	 * @param	string	$dir			目录地址
	 * @param	string	$ext		转换的文件格式
	 * @return	string	如果原字符集和目标字符集相同则返回false，否则为true
	 */
	public static function iconvDir($in_charset, $out_charset, $dir, $ext = 'php|html|htm|shtml|shtm|js|txt|xml')
    {
		if($in_charset == $out_charset) return false;

		$list = self::listDir($dir);
		foreach($list as $v)
        {
			if (preg_match("/\.($ext)/i", $v) && is_file($v))
				file_put_contents($v, iconv($in_charset, $out_charset, file_get_contents($v)));
		}

		return true;
	}

	/**
	 * 批量修改目录下面的所有文件的访问和修改时间
	 *
	 * @param	string	$path		路径
	 * @param	int		$modifyTime		修改时间
	 * @param	int		$accessTime		访问时间
	 * @return	array	不是目录时返回false，否则返回 true
	 */
	public static function touchDir($path, $modifyTime, $accessTime)
    {
        $path = self::parsePath($path);
		if (!is_dir($path)) return false;

		touch($path, $modifyTime, $accessTime);
		$files = glob($path.'*');
		foreach($files as $v)
        {
			is_dir($v) ? self::touchDir($v, $modifyTime, $accessTime) : touch($v, $modifyTime, $accessTime);
		}

		return true;
	}

    /**
     * 获取目录信息
     *
     * @static
     * @param $dir
     * @return array
     */
    public static function getDirInfo($dir)
    {
        return is_dir($dir) ? stat($dir) : array();
    }

	/**
	 * 获取目录树结构数组
	 *
     * @static
	 * @param	string	$dir		 路径
	 * @param	int		$parentId	 父id
	 * @param	array	$dirs		 传入的目录
     * @param   int     $returnLevel 返回目录层级
     * @param   int     $currentLevel 当前层级
	 * @return	array	返回目录列表
	 */
	public static function getDirTree($dir, $parentId = 0, $dirs = array(), $returnLevel = 0, $currentLevel = 1)
    {
		global $id;
		if ($parentId == 0) $id = 0;
		$list = glob($dir.'*');
		foreach($list as $v)
        {
			if (is_dir($v))
            {
				$id++;
				$dirs[$id] = array('id'=>$id,'parentId'=>$parentId, 'name'=>basename($v), 'dir'=>$v.DS);
				if ($returnLevel == 0 || $currentLevel < $returnLevel) $dirs = self::getDirTree($v.DS, $id, $dirs, $returnLevel, $currentLevel+1);
			}
		}
		return $dirs;
	}

	/**
	 * 转化 \ 为 /
	 *
     * @static
	 * @param	string	$path	路径
	 * @return	string	路径
	 */
	public static function parsePath($path)
    {
		$path = str_replace('\\', '/', $path);
		if (substr($path, -1) != '/') $path = $path.'/';

		return $path;
	}

    /**
     * 获取文件后缀
     *
     * @static
     * @param   string   $file
     * @return   string
     */
    public static function getExt($file)
    {
        return str_replace('.', '', strrchr($file, '.'));
    }

    /**
     * 取给定文件文件名，不包括扩展名。
     * example: getbaseName("j:/xx.jpg"); //返回 xx
     *
     * @param string $filename 给定要取文件名的文件
     * @param string $type 扩展名
     * @return string 返回文件名
     */
	public static function getBaseName($filename, $type)
    {
		$baseName = baseName($filename, $type);
		return $baseName;
	}

    /**
     * 获取文件的mime类型
     *
     * @static
     * @param $filename
     * @return string
     */
    public static function mimeCodeMap($filename)
    {
        $maps = array(
			'ai'	=> 'application/postscript',
			'aif'	=> 'audio/x-aiff',
			'aifc'	=> 'audio/x-aiff',
			'aiff'	=> 'audio/x-aiff',
			'asc'	=> 'application/pgp',
			'asf'	=> 'video/x-ms-asf',
			'asx'	=> 'video/x-ms-asf',
			'au'	=> 'audio/basic',
			'avi'	=> 'video/x-msvideo',
			'bcpio'	=> 'application/x-bcpio',
			'bin'	=> 'application/octet-stream',
			'bmp'	=> 'image/bmp',
			'c'   	=> 'text/plain',
			'cc'	=> 'text/plain',
			'cs'	=> 'text/plain',
			'cpp'	=> 'text/x-c++src',
			'cxx'	=> 'text/x-c++src',
			'cdf'	=> 'application/x-netcdf',
			'class'	=> 'application/octet-stream',
			'com'	=> 'application/octet-stream',
			'cpio'	=> 'application/x-cpio',
			'cpt'	=> 'application/mac-compactpro',
			'csh'	=> 'application/x-csh',
			'css'	=> 'text/css',
			'csv'	=> 'text/comma-separated-values',
			'dcr'	=> 'application/x-director',
			'diff'	=> 'text/diff',
			'dir'	=> 'application/x-director',
			'dll'	=> 'application/octet-stream',
			'dms'	=> 'application/octet-stream',
			'doc'	=> 'application/msword',
			'dot'	=> 'application/msword',
			'dvi'	=> 'application/x-dvi',
			'dxr'	=> 'application/x-director',
			'eps'	=> 'application/postscript',
			'etx'	=> 'text/x-setext',
			'exe'	=> 'application/octet-stream',
			'ez'	=> 'application/andrew-inset',
			'gif'	=> 'image/gif',
			'gtar'	=> 'application/x-gtar',
			'gz'	=> 'application/x-gzip',
			'h'	    => 'text/plain',
			'h++'	=> 'text/plain',
			'hh'	=> 'text/plain',
			'hpp'	=> 'text/plain',
			'hxx'	=> 'text/plain',
			'hdf'	=> 'application/x-hdf',
			'hqx'	=> 'application/mac-binhex40',
			'htm'	=> 'text/html',
			'html'	=> 'text/html',
			'ice'	=> 'x-conference/x-cooltalk',
			'ics'	=> 'text/calendar',
			'ief'	=> 'image/ief',
			'ifb'	=> 'text/calendar',
			'iges'	=> 'model/iges',
			'igs'	=> 'model/iges',
			'jar'	=> 'application/x-jar',
			'java'	=> 'text/x-java-source',
			'jpe'	=> 'image/jpeg',
			'jpeg'	=> 'image/jpeg',
			'jpg'	=> 'image/jpeg',
			'js'	=> 'application/x-javascript',
			'kar'	=> 'audio/midi',
			'latex'	=> 'application/x-latex',
			'lha'	=> 'application/octet-stream',
			'log'	=> 'text/plain',
			'lzh'	=> 'application/octet-stream',
			'm3u'	=> 'audio/x-mpegurl',
			'man'	=> 'application/x-troff-man',
			'me'	=> 'application/x-troff-me',
			'mesh'	=> 'model/mesh',
			'mid'	=> 'audio/midi',
			'midi'	=> 'audio/midi',
			'mif'	=> 'application/vnd.mif',
			'mov'	=> 'video/quicktime',
			'movie'	=> 'video/x-sgi-movie',
			'mp2'	=> 'audio/mpeg',
			'mp3'	=> 'audio/mpeg',
			'mpe'	=> 'video/mpeg',
			'mpeg'	=> 'video/mpeg',
			'mpg'	=> 'video/mpeg',
			'mpga'	=> 'audio/mpeg',
			'ms'	=> 'application/x-troff-ms',
			'msh'	=> 'model/mesh',
			'mxu'	=> 'video/vnd.mpegurl',
			'nc'	=> 'application/x-netcdf',
			'oda'	=> 'application/oda',
			'patch'	=> 'text/diff',
			'pbm'	=> 'image/x-portable-bitmap',
			'pdb'	=> 'chemical/x-pdb',
			'pdf'	=> 'application/pdf',
			'pgm'	=> 'image/x-portable-graymap',
			'pgn'	=> 'application/x-chess-pgn',
			'pgp'	=> 'application/pgp',
			'php'	=> 'application/x-httpd-php',
			'php3'	=> 'application/x-httpd-php3',
			'pl'	=> 'application/x-perl',
			'pm'	=> 'application/x-perl',
			'png'	=> 'image/png',
			'pnm'	=> 'image/x-portable-anymap',
			'po'	=> 'text/plain',
			'ppm'	=> 'image/x-portable-pixmap',
			'ppt'	=> 'application/vnd.ms-powerpoint',
			'ps'	=> 'application/postscript',
			'qt'	=> 'video/quicktime',
			'ra'	=> 'audio/x-realaudio',
			'rar'   => 'application/octet-stream',
			'ram'	=> 'audio/x-pn-realaudio',
			'ras'	=> 'image/x-cmu-raster',
			'rgb'	=> 'image/x-rgb',
			'rm'	=> 'audio/x-pn-realaudio',
			'roff'	=> 'application/x-troff',
			'rpm'	=> 'audio/x-pn-realaudio-plugin',
			'rtf'	=> 'text/rtf',
			'rtx'	=> 'text/richtext',
			'sgm'	=> 'text/sgml',
			'sgml'	=> 'text/sgml',
			'sh'	=> 'application/x-sh',
			'shar'	=> 'application/x-shar',
			'shtml'	=> 'text/html',
			'silo'	=> 'model/mesh',
			'sit'	=> 'application/x-stuffit',
			'skd'	=> 'application/x-koan',
			'skm'	=> 'application/x-koan',
			'skp'	=> 'application/x-koan',
			'skt'	=> 'application/x-koan',
			'smi'	=> 'application/smil',
			'smil'	=> 'application/smil',
			'snd'	=> 'audio/basic',
			'so'	=> 'application/octet-stream',
			'spl'	=> 'application/x-futuresplash',
			'src'	=> 'application/x-wais-source',
			'stc'	=> 'application/vnd.sun.xml.calc.template',
			'std'	=> 'application/vnd.sun.xml.draw.template',
			'sti'	=> 'application/vnd.sun.xml.impress.template',
			'stw'	=> 'application/vnd.sun.xml.writer.template',
			'sv4cpio'	=> 'application/x-sv4cpio',
			'sv4crc'	=> 'application/x-sv4crc',
			'swf'	=> 'application/x-shockwave-flash',
			'sxc'	=> 'application/vnd.sun.xml.calc',
			'sxd'	=> 'application/vnd.sun.xml.draw',
			'sxg'	=> 'application/vnd.sun.xml.writer.global',
			'sxi'	=> 'application/vnd.sun.xml.impress',
			'sxm'	=> 'application/vnd.sun.xml.math',
			'sxw'	=> 'application/vnd.sun.xml.writer',
			't'	    => 'application/x-troff',
			'tar'	=> 'application/x-tar',
			'tcl'	=> 'application/x-tcl',
			'tex'	=> 'application/x-tex',
			'texi'	=> 'application/x-texinfo',
			'texinfo'	=> 'application/x-texinfo',
			'tgz'	=> 'application/x-gtar',
			'tif'	=> 'image/tiff',
			'tiff'	=> 'image/tiff',
			'tr'	=> 'application/x-troff',
			'tsv'	=> 'text/tab-separated-values',
			'txt'	=> 'text/plain',
			'ustar'	=> 'application/x-ustar',
			'vbs'	=> 'text/plain',
			'vcd'	=> 'application/x-cdlink',
			'vcf'	=> 'text/x-vcard',
			'vcs'	=> 'text/calendar',
			'vfb'	=> 'text/calendar',
			'vrml'	=> 'model/vrml',
			'vsd'	=> 'application/vnd.visio',
			'wav'	=> 'audio/x-wav',
			'wax'	=> 'audio/x-ms-wax',
			'wbmp'	=> 'image/vnd.wap.wbmp',
			'wbxml'	=> 'application/vnd.wap.wbxml',
			'wm'	=> 'video/x-ms-wm',
			'wma'	=> 'audio/x-ms-wma',
			'wmd'	=> 'application/x-ms-wmd',
			'wml'	=> 'text/vnd.wap.wml',
			'wmlc'	=> 'application/vnd.wap.wmlc',
			'wmls'	=> 'text/vnd.wap.wmlscript',
			'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
			'wmv'	=> 'video/x-ms-wmv',
			'wmx'	=> 'video/x-ms-wmx',
			'wmz'	=> 'application/x-ms-wmz',
			'wrl'	=> 'model/vrml',
			'wvx'	=> 'video/x-ms-wvx',
			'xbm'	=> 'image/x-xbitmap',
			'xht'	=> 'application/xhtml+xml',
			'xhtml'	=> 'application/xhtml+xml',
			'xls'	=> 'application/vnd.ms-excel',
			'xlt'	=> 'application/vnd.ms-excel',
			'xml'	=> 'application/xml',
			'xpm'	=> 'image/x-xpixmap',
			'xsl'	=> 'text/xml',
			'xwd'	=> 'image/x-xwindowdump',
			'xyz'	=> 'chemical/x-xyz',
			'z'	    => 'application/x-compress',
			'zip'	=> 'application/zip',
        );

        $ext = self::getExt($filename);
        return isset($maps[$ext]) ? $maps[$ext] : '';
    }

    public function __destruct()
    {
        $this->handle && $this->close();
    }
}