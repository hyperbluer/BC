<?php
/**
 * Result.class.php 数据库查询结果返回类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-03
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Database_Driver_Mysql_Result extends Database_ResultAbstract
{
	public $resultType = MYSQL_ASSOC;

    /**
     * 获取单条记录
     * 
     * @see Database_ResultAbstract::get()
     * @param string $field
     * @return array|string
     */
    public function getOne($field = '')
    {
        $row = array();
        if (is_resource($this->result))
        {
           $row = mysql_fetch_array($this->result, $this->resultType);
        }

        return $field ? (isset($row[$field]) ? $row[$field] : '') : $row;
    }

    /**
     * 返回SQL语句执行的最终结果
     * 资源标识符类型，返回多条记录方式可选：MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH
     *
     * @see Database_ResultAbstract::result()
     * @return array
     */
    public function result()
    {
        if (is_resource($this->result))
        {
            $rows = array();
            while(($row = mysql_fetch_array($this->result, $this->resultType)) != false)
            {
                $rows[] = $row;
            }

            return $rows;
        }
        else
        {
            return $this->result;
        }
    }

    /**
     * 获取语句执行影响行数
     * 
     * @return int
     */
    public function count()
    {
        return is_resource($this->result) ? mysql_num_rows($this->result) : 0;
    }

    /**
     * 释放结果内存
     *
     * @return bool
     */
    public function free()
    {
        return is_resource($this->result) && mysql_free_result($this->result);
    }

    /**
     * 析构函数
     * 自动释放结果内存
     */
    public function __destruct()
    {
        $this->free();
    }
}
