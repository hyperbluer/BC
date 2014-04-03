<?php
/**
 * 定义基本路径常量、自动装载类库及函数等
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-08
 */

// PHP版本检测
if (version_compare(phpversion(), '5.1.0', '<') == TRUE) {
	exit('PHP5.1+ Required');
}

//定义基本路径及常量
!defined('START_TIME') && define('START_TIME', microtime( TRUE ));
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);
!defined('BASE_PATH') && define('BASE_PATH', realpath(dirname(__FILE__)).DS.'..'.DS);
!defined('BC_PATH') && define('BC_PATH', str_replace('\\','/',realpath(dirname(__FILE__)).DS));
!defined('BC_CORE_PATH') && define('BC_CORE_PATH', BC_PATH.'BC'.DS);

//定义框架基本信息及类库物理路径
define('IN_BC',             true);
define('BC_VERSION',        '0.1'); //版本
define('BC_SYS_TIME',       time()); //当前时间戳
define('BC_CLS_PATH',       BC_PATH.'Classes'.DS); //类库
define('BC_FUNC_PATH',      BC_PATH.'Functions'.DS); //通用函数库
define('BC_ERROR_PAGE_PATH',BASE_PATH.'error_pages'.DS); //错误页面路径

//定义APP项目文件夹物理路径
define('APP_MOD_PATH',      APP_PATH.'modules'.DS); //模块
!defined('APP_CFG_PATH') && define('APP_CFG_PATH',      APP_PATH.'configs'.DS); //配置
!defined('APP_LANG_PATH') && define('APP_LANG_PATH',     APP_PATH.'languages'.DS); //语言包
!defined('APP_LIB_PATH') && define('APP_LIB_PATH',      APP_PATH.'libraries'.DS); //类包
!defined('APP_EXT_PATH') && define('APP_EXT_PATH',      APP_PATH.'plugins'.DS); //扩展插件
!defined('APP_TPL_PATH') && define('APP_TPL_PATH',      APP_PATH.'templates'.DS); //模板
!defined('APP_VAR_PATH') && define('APP_VAR_PATH',      APP_PATH.'var'.DS); //临时数据
!defined('APP_CACHE_PATH') && define('APP_CACHE_PATH',    APP_VAR_PATH.'cache'.DS); //缓存
!defined('APP_LOG_PATH') && define('APP_LOG_PATH',      APP_VAR_PATH.'log'.DS); //日志
!defined('APP_UPLOAD_PATH') && define('APP_UPLOAD_PATH',   BASE_PATH.'assets'.DS.'upload'.DS); //上传文件

//TODO 判断系统是否成功安装


//自动装载类库
BC::$includePath = array
(
    BC_CORE_PATH,
    BC_CLS_PATH,
);
spl_autoload_register(array( 'BC', 'autoLoad' ));
