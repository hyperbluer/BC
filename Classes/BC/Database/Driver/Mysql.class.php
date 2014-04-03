<?php
/**
 * Mysql.class.php 数据库驱动接口类
 *
 * TODO Mysql stmt预处理
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-03
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Database_Driver_Mysql extends  Database_DriverAbstract
{
    /**
     * 数据库连接
     *
     * @see Database_DriverAbstract::connect()
     * @abstract
     * @return boolean
     */
    public function connect()
    {
        try
        {
            $this->connection = mysql_connect($this->config['hostname'],$this->config['username'],$this->config['password']);
            $this->selectDB($this->config['database']); //选择数据库
            $this->setCharset(); //设置编码

            return true;
        }
        catch (Exception $e)
        {
            //throw new BC_Exception($e);
            return false;
        }
    }

    /**
     * 执行数据库SQL操作
     *
     * @see Database_DriverAbstract::query()
     * @abstract
     * @param $queryData
     * @return boolean|Database_Driver_Mysql_Result
     */
    public function query($queryData)
    {
        $sql = $this->toSql($queryData);
        $result = mysql_query($sql, $this->connection);
        if($result === false)
		{
			return false;
		}

        if ( $this->action === 'INSERT' || $this->action === 'REPLACE' )
        {
            $result = array
            (
                $this->getLastInsertId(),
                $this->getAffectedRows()
            );
        }
        elseif ( $this->action === 'UPDATE' || $this->action === 'DELETE' )
        {
            $result = array(
                $this->getAffectedRows()
            );
        }

        $resultObject = new Database_Driver_Mysql_Result();
        $resultObject->setResult($result);
        return $resultObject;
    }

    public function select($queryData)
    {
        $sqlArray = array(
            'SELECT',
            $this->parseField($queryData['field']),
            'FROM',
            $this->parseTable($queryData['table']),
            $this->parseWhere($queryData['where']),
            $this->parseOrder($queryData['order']),
            $this->parseLimit($queryData['limit'], $queryData['offset']),
        );

        $sql = implode(' ', $sqlArray);

        return $sql;
    }

    public function insert($queryData)
    {
        if (!is_array($queryData['field']) && is_array($queryData['values']))
        {
            $keys = array_keys($queryData['values']);
            !is_numeric($keys[0]) && $queryData['field'] = array_keys($queryData['values']);
        }

        $sqlArray = array(
            'INSERT INTO ',
            $this->parseTable($queryData['table']),
            '(',
            $this->parseField($queryData['field']),
            ') VALUES (',
            $this->parseValue($queryData['values']),
            ')',
        );

        $sql = implode(' ', $sqlArray);

        return $sql;
    }

    public function update($queryData)
    {
        $sqlArray = array(
            'UPDATE',
            $this->parseTable($queryData['table']),
            $this->parseSet($queryData['values'], $queryData['field']),
            $this->parseWhere($queryData['where']),
            $this->parseOrder($queryData['order']),
            $this->parseLimit($queryData['limit'], $queryData['offset']),
        );

        $sql = implode(' ', $sqlArray);

        return $sql;
    }

    public function delete($queryData)
    {
        $sqlArray = array(
            'DELETE',
            'FROM',
            $this->parseTable($queryData['table']),
            $this->parseWhere($queryData['where']),
            $this->parseOrder($queryData['order']),
            $this->parseLimit($queryData['limit'], $queryData['offset']),
        );

        $sql = implode(' ', $sqlArray);

        return $sql;
    }
    
    public function selectDB($database)
    {
        try
        {
            mysql_select_db($database);
        }
        catch (Exception $e)
        {
            throw new BC_Exception($e);
        }

    }

    public function createDB($database)
    {
        return "CREATE DATABASE ".$database;
    }

    public function dropDB($database)
    {
        return "DROP DATABASE ".$database;
    }

    public function create($queryData)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '.
               $this->parseTable($queryData['table']).' ('.
                $this->parseStructureColumns($queryData['structure']['columns']).
                $this->parsePrimaryKey($queryData['structure']['primary_keys']).
                $this->parseKey($queryData['structure']['keys']).
               ') ENGINE='.$queryData['structure']['engine'].
               ' DEFAULT CHARSET='.$this->config['charset'].';';

        return $sql;
    }

    public function alter($queryData)
    {
        !in_array(strtoupper($queryData['structure']['alter_type']), array('ADD', 'DROP', 'CHANGE')) && $queryData['structure']['alter_type'] = 'ADD';

        $sqlArray = array(
            'ALTER',
            'TABLE',
            $this->parseTable($queryData['table']),
            $queryData['structure']['alter_type'],
            $this->parseStructureColumns($queryData['structure']['columns']),
        );

        $sql = implode(' ', $sqlArray);

        $queryData['structure']['after_column'] && $sql .= ' AFTER ' . $this->parseField($queryData['structure']['after_column']);

		return $sql;
    }

    public function drop($queryData)
    {
        return "DROP TABLE IF EXISTS ".$this->parseTable($queryData['table']);
    }

    public function rename($queryData)
    {
        $sqlArray = array(
            'ALTER',
            'TABLE',
            $this->parseTable($queryData['table']),
            ' RENAME TO ',
            $this->parseTable($queryData['renameTable']),
        );

        $sql = implode(' ', $sqlArray);

		return $sql;
    }

    public function setCharset()
    {
        if (function_exists('mysql_set_charset'))
        {
            mysql_set_charset($this->config['charset'], $this->connection);
        }
    }

    public function getVersion()
    {
        return mysql_get_server_info($this->connection);
    }

    /**
     * 获取插入新记录id
     *
     * @see Database_DriverAbstract::getLastInsertId()
     * @abstract
     * @return int
     */
    public function getLastInsertId()
    {
        return mysql_insert_id($this->connection);
    }

    /**
     * 获取SQL执行影响行数
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->connection);
    }

    /**
     * 关闭数据连接
     *
     * @see Database_DriverAbstract::close()
     * @abstract
     * @return void
     */
	public function close()
    {
        mysql_close($this->connection);
    }

    /**
     * 返回数据库错误信息
     *
     * @see Database_DriverAbstract::error()
     * @abstract
     * @return string
     */
	public function error()
    {
        return mysql_error();
    }
}