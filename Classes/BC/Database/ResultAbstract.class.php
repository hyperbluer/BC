<?php
/**
 * Result.class.php 数据库查询结果返回类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-01
 */
 
defined('IN_BC') or exit("Access Denied!");

abstract class BC_Database_ResultAbstract
{
    /**
     * 数据库查询结果集
     * SELECT，SHOW，EXPLAIN 或 DESCRIBE操作返回为资源标识符
     * 其他类型SQL返回Boolean类型，非关系型数据库直接返回查询结果
     *
     * @var
     */
    protected $result;

    /**
     * 设置SQL语句执行结果句柄
     *
     * @param $result   SQL语句执行结果
     * @return void
     */
    public function setResult($result)
    {
         $this->result = $result;
    }

    /**
     * 获取SQL语句执行结果句柄
     *
     * @return
     */
    public function getResult()
    {
         return $this->result;
    }

    /**
     * 抽象方法，获取单条记录
     *
     * @abstract
     * @param string $field 默认为空，返回单条记录数组。非空则返回相应字段赋值
     * @return void
     */
    abstract public function getOne($field = '');

    /**
     * 抽象方法，返回SQL语句执行的最终结果。
     * 当$this->result为资源标识符，则返回记录集合。其他类型直接输出执行结果
     *
     * @abstract
     * @return void
     */
    abstract public function result();
}
