<?php
/**
 * Result.class.php 数据库查询结果返回类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-02
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Database_Driver_Sqlite_Result extends Database_ResultAbstract
{
    /**
     * 获取单条记录
     *
     * @see Database_ResultAbstract::get()
     * @param string $field
     * @return array|string
     */
    public function getOne($field = '')
    {

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
        return $this->result;
    }
}
