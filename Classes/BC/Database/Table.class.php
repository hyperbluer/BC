<?php
/**
 * Table.class.php 数据库表处理类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-01
 */
 
defined('IN_BC') or exit("Access Denied!");

class BC_Database_Table
{
    /**
     * 数据表数组
     *
     * @var
     */
    public $data;

    /**
     * 设置操作的表名，
     * 非关系型数据库mongo则称为collection
     *
     * @param $tableName
     * @return BC_Database_Table
     */
    public function table($tableName)
    {
        $this->data['table'] = $tableName;
        return $this;
    }

    /**
     * 设置操作的字段
     *
     * @param string|array $field 格式建议采取array('field1', 'field2');
     * @return BC_Database_Table
     */
    public function field($field = '*')
    {
        $this->data['field'] = $field;
        
        return $this;
    }

    /**
     * 设置字段赋值
     *
     * @param string|array $values 插入时格式建议保存和field一值，更新操作时格式建议采取array('field1' => 'value1', 'field2' => 'value2')
     * @return BC_Database_Table
     */
    public function values($values = '')
    {
        $this->data['values'] = $values;

        return $this;
    }

    /**
     * 设置索引
     *
     * @param string|array $indexes
     * @return void
     */
    public function indexes($indexes = '')
    {
        $this->data['indexes'] = $indexes;

        return $this;
    }

    public function newTable($table)
    {
        $this->data['renameTable'] = $table;

        return $this;
    }

    public function structure($data)
    {
        $defaultData = array(
            'alter_type' => '', //ADD, DROP, CHANGE
            'columns' => '',
            'after_column' => '',
            'primary_keys' => array(),
            'keys' => array(),
            'engine' => 'MyISAM',
            'charset' => 'utf8',
        );
        $data = ArrayConvert::mergeArray($defaultData, $data);
        $this->data['structure'] = $data;

        return $this;
    }

    /**
     * 重置设置的数据表数组
     *
     * @return BC_Database_Table
     */
    public function reset()
    {
        $this->data = array();

        return $this;
    }
}
