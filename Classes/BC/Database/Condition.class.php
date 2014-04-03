<?php
/**
 * Condition.class.php 数据库查询条件解析类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-19
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Database_Condition
{
    /**
     * 算术运算符
     *
     * @var array
     */
    public static $arithmeticChars = array('=', 'IS', '!=', 'IS NOT', 'LIKE', 'NOT LIKE', '<', '>', '<=', '>=', 'IN');

    /**
     * 逻辑运算符
     *
     * @var array
     */
    public static $logicChars = array('AND', 'OR', '&&', '||');

    /**
     * 条件数组
     *
     * @var
     */
    public $data = array();

    /**
     * 设置where语句
     *
     * @param null|array|string $wheres 支持sql语法及数组传递方式。
     * @return BC_Database_Condition
     */
    public function where($wheres = null)
    {
        $this->data['where'] = $wheres;
        return $this;
    }

    /**
     * 设置排序语句
     *
     * @param null|array|string $orders 支持sql语法及数组传递方式。
     * @return BC_Database_Condition
     */
    public function order($orders = null)
    {
        $this->data['order'] = $orders;
        return $this;
    }

    /**
     * 设置记录读取起始位置
     *
     * @param $n
     * @return BC_Database_Condition
     */
    public function offset($n)
    {
        $this->data['offset'] = $n;
        return $this;
    }

    /**
     * 设置读取记录数
     *
     * @param $n
     * @return BC_Database_Condition
     */
    public function limit($n)
    {
        $this->data['limit'] = $n;
        return $this;
    }

    /**
     * 设置附加参数
     *
     * @param array $options
     * @return BC_Database_Condition
     */
    public function options($options = array())
    {
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * 重置条件数据
     *
     * @return BC_Database_Condition
     */
    public function reset()
    {
        $this->data = array();
       
        return $this;
    }
    
}
