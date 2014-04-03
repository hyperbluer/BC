<?php
/**
 * Database.class.php 数据库驱动类
 *
 * 统一规范数据库共享方法
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-03
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Database
{
    protected $driver;
    protected $table;
    protected $condition;
    public static $instances = array();
    protected $queryData = array();
    protected $defaultQueryData = array(
        'action' => '',
        'table' => '',
        'field' => '',
        'values' => '',
        'indexes' => '',
        'options' => '',
        'where' => '',
        'order' => '',
        'limit' => '',
        'offset' => ''
    );

    public function __construct($config = array())
    {
        $driverName = 'Database_Driver_'.ucfirst($config['driver']);
        if (!class_exists($driverName))
        {
            throw new BC_Exception('Database Driver:' . $driverName . ' haven\'t found!');
        }

        $this->driver = new $driverName($config);
        if ($this->driver->connect() !== true)
        {
            throw new BC_Exception('Couldn\'t connect to database![Driver:'.$config['driver'].']');
        }
    }

    public static function instance($config = array())
    {
        if ( is_array($config) )
        {
            $key = 'BC_' . md5(serialize($config));
        }
        else
        {
            $key = 'BC_Database';
        }
        if ( ! isset(Database::$instances[$key]) )
        {
            Database::$instances[$key] = new Database($config);
        }

        return Database::$instances[$key];
    }

    public function table(Database_Table $table)
    {
        $this->table = $table;
        $this->queryData = ArrayConvert::mergeArray($this->queryData, $this->table->data);
        
        return $this;
    }

    public function condition(Database_Condition $condition)
    {
        $this->condition = $condition;
        $this->queryData = ArrayConvert::mergeArray($this->queryData, $this->condition->data);

        return $this;
    }

    public function query($sql = '')
    {
        empty($sql) && $sql = $this->getQueryData();
        $result = $this->driver->query($sql);
        
        if ($result instanceof Database_ResultAbstract)
        {
            return $result;
        }
        else
        {
            if (is_string($this->error()))
                throw new BC_Exception($this->error());
            else
                throw new BC_Exception('SQL语句执行失败');
        }
    }

    public function toSql()
    {
        return $this->driver->toSql($this->getQueryData());
    }

    public function getQueryData()
    {
        $queryData = ArrayConvert::mergeArray($this->defaultQueryData, $this->queryData);
        $this->queryData = array(); //重置保存的查询条件

        return $queryData;
    }

    public function select()
    {
        $this->queryData['action'] = 'SELECT';

        return $this;
    }

    public function insert()
    {
        $this->queryData['action'] = 'INSERT';

        return $this;
    }

    public function update()
    {
        $this->queryData['action'] = 'UPDATE';

        return $this;
    }

    public function delete()
    {
        $this->queryData['action'] = 'DELETE';

        return $this;
    }

    public function replace()
    {
        $this->queryData['action'] = 'REPLACE';

        return $this;
    }

    public function create()
    {
        $this->queryData['action'] = 'CREATE';

        return $this;
    }

    public function alter()
    {
        $this->queryData['action'] = 'ALTER';

        return $this;
    }

    public function drop()
    {
        $this->queryData['action'] = 'DROP';

        return $this;
    }

    public function rename()
    {
        $this->queryData['action'] = 'RENAME';

        return $this;
    }

    public function getPrefix()
    {
         return $this->driver->getPrefix();
    }

    /**
     * 获取驱动引擎对象
     * 
     * @return
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * 获取驱动引擎版本号
     *
     * @return void
     */
    public function getVersion()
    {
        $this->driver->getVersion();
    }

    /**
     * 关闭数据库连接
     * @return void
     */
    public function close()
    {
        $this->driver->close();
    }

    public function error()
    {
        return $this->driver->error();
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->close();
    }
}