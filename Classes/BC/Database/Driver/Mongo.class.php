<?php
/**
 * TODO Mongo.class.php Mongo数据库驱动类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-01
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Database_Driver_Mongo extends Database_DriverAbstract
{
    /**
     * 数据库连接
     *
     * @see Database_DriverInterface::connect()
     * @abstract
     * @return boolean
     */
    public function connect()
    {
        if (($class = self::isSupport()) === false)
            throw new BC_Exception("The MongoDB PECL extension has not been installed or enabled");

        try
        {
            $this->connection = new $class($this->getServer(), $this->config['options']);
            $this->selectDB($this->config['database']);
            return true;
        }
        catch (MongoConnectionException $e)
        {
            //throw new BC_Exception($e);
            return false;
        }
        
    }

    /**
     * Mongo命令行
     *
     * @throws BC_Exception
     * @param array $query
     * @return array
     */
    public function command($query = array())
    {
        try {
            $result = $this->db->command($query);

            return $result;
        }
        catch (MongoCursorException $e)
        {
            throw new BC_Exception('MongoDB command failed to execute: ' .$e->getMessage());
        }
    }

    /**
     * 运行javascript代码
     *
     * @throws BC_Exception
     * @param $code
     * @param array $args
     * @return array
     */
    public function execute($code, $args = array())
    {
        try {
            $result = $this->db->execute($code, $args);

            return $result;
        }
        catch (MongoCursorException $e)
        {
            throw new BC_Exception('MongoDB command failed to execute: ' .$e->getMessage());
        }
    }

    /**
     * 执行数据库操作
     *
     * @see Database_DriverInterface::query()
     * @abstract
     * @param $queryData
     * @return Database_Driver_Mongo_Result|MongoCursorException
     */
    public function query($queryData)
    {
        try
        {
            $result = $this->toSql($queryData);

            $resultObject = new Database_Driver_Mongo_Result();
            $resultObject->setResult($result);
            return $resultObject;
        }
        catch (MongoCursorException $e)
        {
            throw new BC_Exception($e);
        }
    }

    public function select($queryData)
    {
        $cursor = $this->db->{$this->parseTable($queryData['table'])}->
                find($this->parseWhere($queryData['where']), $this->parseField($queryData['field']));
        if ($queryData['order'])
        {
            $cursor = $cursor->sort($this->parseOrder($queryData['order']));
        }
        if ($queryData['offset'])
        {
            $cursor = $cursor->skip($this->parseSkip($queryData['offset']));
        }
        if ($queryData['limit'])
        {
            $cursor = $cursor->limit($this->parseLimit($queryData['limit']));
        }

        return $cursor;
    }

    public function insert($queryData)
    {
        $cursor = $this->db->{$this->parseTable($queryData['table'])}->
                insert($this->parseValue($queryData['values']),
                       $this->parseOptions($queryData['options']));
    
        return $cursor;
    }

    public function update($queryData)
    {
        $cursor = $this->db->{$this->parseTable($queryData['table'])}->
                update($this->parseWhere($queryData['where']),
                       $this->parseSet($queryData['values'], $queryData['field']),
                       $this->parseOptions($queryData['options']));

        return $cursor;
    }

    public function delete($queryData)
    {
        $cursor = $this->db->{$this->parseTable($queryData['table'])}->
                remove($this->parseWhere($queryData['where']),
                       $this->parseOptions($queryData['options']));

        return $cursor;
    }

    public function addIndex($queryData)
    {
        $keys = is_array($queryData['indexes']) ? $this->parseOrder($queryData['indexes']) : $queryData['indexes'];
        return $this->db->{$this->parseTable($queryData['table'])}->
                ensureIndex($keys, $this->parseOptions($queryData['options']));
    }

    public function deleteIndex($queryData)
    {
        $keys = is_array($queryData['indexes']) ? $this->parseOrder($queryData['indexes']) : $queryData['indexes'];
        return $this->db->{$this->parseTable($queryData['table'])}->
                deleteIndex($keys, $this->parseOptions($queryData['options']));
    }

    public function getIndexes($queryData)
    {
        return $this->db->{$this->parseTable($queryData['table'])}->
                getIndexInfo();
    }

    protected function parseField($fields)
    {
        $array = array();
        if (is_array($fields))
        {
            foreach ($fields as $field)
            {
                $array[$this->quoteField($field)] = 1;
            }

        }
        elseif (is_string($fields) && !empty($fields))
        {
            $array[$this->quoteField($fields)] = 1;
        }
        else
        {
        }

        return $array;
    }

    protected function parseValue($values)
    {
        return is_array($values) ? $values : array();
    }

    protected function parseSet($values, $fields = NULL)
    {
        !is_array($values) && $values = array();
        
        return array('$set' => $values);
    }

    protected function parseTable($tables)
    {
        return $tables;
    }

    protected function parseWhere($where)
    {
        return is_array($where) ? $where : array();
    }

    protected function parseOrder($order)
    {
        $array = array();
        
        if (is_array($order))
        {
            foreach ($order as $key => $value)
            {
                $value = ($value == 1 || strtolower($value) == 'asc') ? 1 : -1;
                $array[$this->quoteField($key)] = $value;
            }
        }

        return $array;
    }

    protected function parseSkip($skip)
    {
        return (int)$skip;
    }

    protected function parseLimit($limit, $offSet = 0)
    {
        return (int)$limit;
    }

    protected function parseJoin($join)
    {

    }

    protected function parseGroup($group)
    {

    }

    protected function parseDistinct($distinct)
    {
        
    }

    /**
     * Mongo CURD操作设置可选项
     *
     * @param $options
     * @return array
     */
    protected function parseOptions($options)
    {
        $defaultOptions = array(
            'fsync' => FALSE, //是否同步
            'multiple' => FALSE //是否批量操作
        );
        $options = is_array($options) ? ArrayConvert::mergeArray($defaultOptions, $options) : $defaultOptions;

        return $options;
    }

    /**
     * 选择文档
     *
     * @throws BC_Exception
     * @param $database
     * @return void
     */
    public function selectDB($database)
    {
        try
        {
            $this->db = $this->connection->selectDB($database);
        }
        catch (Exception $e)
        {
            throw new BC_Exception($e);
        }

    }

    /**
     * 删除文档
     *
     * @return boolean
     */
    public function dropDB()
    {
        return $this->db->drop();
    }

    public function getServer()
    {
        $server = 'mongodb://'.$this->config['host'].'/';

        return $server;
    }

    /**
     * 获取插入新记录id
     *
     * @see Database_DriverInterface::getLastInsertId()
     * @abstract
     * @return int
     */
    public function getLastInsertId()
    {

    }

    /**
     * 关闭数据连接
     *
     * @see Database_DriverInterface::close()
     * @abstract
     * @return void
     */
	public function close()
    {
        $this->connection && $this->connection->close();
    }

    /**
     * 返回数据库错误信息
     *
     * @see Database_DriverInterface::error()
     * @abstract
     * @return string
     */
    public function error()
    {
        return $this->db->lastError();
    }

    public function setCharset()
    {

    }

    /**
     * 检测运行环境是否支持此数据库客户端扩展
     *
     * @static
     * @return bool|string
     */
    public static function isSupport()
    {
        $class = class_exists('MongoClient', false) ? 'MongoClient' : 'Mongo';
        if (!class_exists($class))
            return false;

        return $class;
    }

    /**
     * 过滤字段，表名
     *
     * @see Database_DriverInterface::quoteField()
     * @static
     * @abstract
     * @param $field
     * @return string
     */
    public static function quoteField($field)
    {
        return $field;
    }

    /**
     * 过滤赋值
     *
     * @see Database_DriverInterface::quoteValue()
     * @static
     * @abstract
     * @param $value
     * @return string
     */
    public static function quoteValue($value)
    {
        return $value;
    }

    /**
     * 析构函数
     * 关闭连接
     */
    public function __destruct()
    {
        $this->close();
    }
}