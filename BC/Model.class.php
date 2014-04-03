<?php
/**
 * Model.php 数据模型基类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-18
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Model
{

    /**
     * 数据库配置
     *
     * @var array
     */
    protected $config;

    /**
     * 数据库句柄
     *
     * @var
     */
    protected $db;

    /**
     * 使用数据库驱动，默认mysql
     *
     * @var string
     */
    protected $driver = 'mysql';
    
    /**
     * 构造函数   
     *
     */
    public function __construct()
    {
        $this->loader = BC::loader();
        $this->session = $this->loader->session;
        //$this->cache = $this->loader->cache;
        //$this->lang = $this->loader->lang;

        !empty($this->driver) && $this->config = BC::config('database/'.$this->driver);
        is_array($this->config) && $this->db = Database::instance($this->config);
    }

    /**
     * 获取表单全名
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->db->getPrefix().$this->tableName;
    }

    /**
     * 设置缓存驱动处理句柄
     *
     * @param $driver
     * @return void
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

}
