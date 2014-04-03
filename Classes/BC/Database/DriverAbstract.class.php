<?php
/**
 * DriverAbstract.class.php 数据库驱动抽象类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-08
 */
 
defined('IN_BC') or exit("Access Denied!");

abstract class BC_Database_DriverAbstract
{
    /**
     * 数据库配置项
     *
     * @var
     */
    protected $config;

    /**
     * 连接句柄
     *
     * @var
     */
    protected $connection;

    /**
     * 当前执行的collection
     *
     * @var
     */
    protected $db;

    /**
     * 当前sql动作，默认为查询（SELECT），列表见self::$actionMaps
     *
     * @var
     */
    protected $action;
    public $actionMaps = array(
            'SELECT',
            'INSERT',
            'UPDATE',
            'DELETE',
            'SHOW',
            'EXPLAIN',
            'DESCRIBE',
            'CREATE',
            'DROP',
            'ALTER',
            'RENAME',
        );

     /**
     * 构造函数，获取配置信息
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }
    
	/**
     * 数据库连接
     *
     * @abstract
     * @return void
     */
	abstract public function connect();

    /**
     * 执行数据库SQL操作，返回执行结果
     *
     * @abstract
     * @param $queryData  默认sql为数组，即table及condition设置的数据集合。针对复杂SQL操作，可直接传递sql语句，忽略table及condition设置
     * @return boolean|\Database_Driver_Mysql_Result
     */
    abstract public function query($queryData);

    /**
     * 获取插入新记录id
     *
     * @abstract
     * @return int
     */
    abstract public function getLastInsertId();

    /**
     * 关闭数据连接
     *
     * @abstract
     * @return void
     */
	abstract public function close();

    /**
     * 返回数据库错误信息
     *
     * @abstract
     * @return string|array
     */
    abstract public function error();

    /**
     * 获取表前缀
     *
     * @return
     */
    public function getPrefix()
    {
        return $this->config['prefix'];
    }

    /**
     * 转换输出sql语句
     *
     * @see Database_DriverInterface::toSql()
     * @abstract
     * @param $queryData 同上
     * @return string
     */
    public function toSql($queryData)
    {
        $this->action = $this->parseAction($queryData);

        if (!is_array($queryData))
            return $queryData;

        $methodName = strtolower($this->action);
        if (!method_exists($this, $methodName))
        {
            throw new BC_Exception('不支持此项SQL操作');
        }
        $sql = $this->$methodName($queryData);

        return $sql;
    }

    /**
     * 解析SQL语句动作，如SELECT, UPDATE, INSERT...
     * 
     * @param $queryData
     * @return string
     */
    protected function parseAction($queryData)
    {
        $action = '';
        if (is_array($queryData) && in_array(strtoupper($queryData['action']), $this->actionMaps))
        {
            $action = $queryData['action'];
        }
        else
        {
            $pattern = implode('|', $this->actionMaps);
            if (preg_match('/^'.$pattern.'/isU', trim($queryData), $matches))
            {
                $action = $matches[0];
            }
        }

        return $action ? strtoupper($action) : 'SELECT';
    }

    /**
     * 转换字段为SQL格式
     *
     * @param $fields
     * @return string
     */
    protected function parseField($fields)
    {
        if (is_array($fields) && count($fields))
        {
            $array   =  array();
            foreach ($fields as $key => $field)
            {
                if(!is_numeric($key))
                    $array[] =  $this->quoteField($key).' AS '.$this->quoteField($field);
                else
                    $array[] =  $this->quoteField($field);
            }

            $fieldsStr = implode(',', $array);
        }
        elseif (is_string($fields) && !empty($fields))
        {
            $fieldsStr = $this->quoteField($fields);
        }
        else
        {
            $fieldsStr = '*';
        }

        return $fieldsStr;
    }

    /**
     * 转换赋值为SQL格式
     *
     * @param $values
     * @return string
     */
    protected function parseValue($values)
    {
        if (is_array($values))
        {
            $array = array();
            foreach ($values as $key=> $value)
            {
                $array[] = $this->quoteValue($value);
            }

            $valuesStr = implode(',', $array);
        }
        else
        {
            $valuesStr = $this->quoteValue($values);
        }

        return $valuesStr;
    }

    /**
     * 转换更新操作数据为SQL格式
     *
     * @param $values
     * @param null $fields
     * @return string
     */
    protected function parseSet($values, $fields = NULL)
    {
        $array = array();

        if ($fields)
        {
            $fields = $this->parseField($fields);
            if (stripos($fields, ',') !== false)
            {
                $fieldsArray = explode(',', $fields);
                foreach ($fieldsArray as $key => $field)
                {
                    $value = (is_array($values) && isset($values[$key])) ? $values[$key] :
                             (is_string($values) ? $values : '');
                    $array[] = $field.'='. $this->quoteValue($value);
                }
            }
            else
            {
                $value = is_array($values) ? $values[0] :
                         (is_string($values) ? $values : '');
                $array = array($fields.'='. $this->quoteValue($value));
            }
        }
        else
        {
            foreach ($values as $key => $value)
            {
                $value = $this->parseValue($value);
                if(is_scalar($value))
                    $array[] = $this->quoteField($key).'='. $value;
            }
        }

        return ' SET '.implode(',',$array);
    }

    /**
     * 转换表为SQL格式
     *
     * @param $tables
     * @return string
     */
    protected function parseTable($tables)
    {
        if(is_string($tables))
            $tables = explode(',', $tables);

        $array =array();
        foreach ($tables as $key=>$table)
        {
            if(is_numeric($key))
            {
                $array[] =  $this->quoteField($this->getPrefix().$table);
            }
            else
            {
                $array[] =  $this->quoteField($this->getPrefix().$key).' '.$this->quoteField($table);
            }
        }

        return implode(',',$array);
    }

    /**
     * 转换条件语句为SQL格式
     *
     * @param $where
     * @return string
     */
    protected function parseWhere($where)
    {
        $whereStr = '';
        if(is_string($where) || empty($where))
        {
            $whereStr = $where;
        }
        else
        {
            $operate = ' AND ';
            foreach ($where as $key => $value)
            {
                $whereStr .= $this->quoteField($key). ' = '. $this->quoteValue($value) . $operate;
            }
            $whereStr = substr($whereStr, 0, -strlen($operate));
        }

        return empty($whereStr) ? '' : ' WHERE '.$whereStr;
    }

    /**
     * 转换排序为SQL格式
     *
     * @param $order
     * @return string
     */
    protected function parseOrder($order)
    {
        if(is_array($order))
        {
            $array = array();
            foreach ($order as $key=>$val)
            {
                if(is_numeric($key))
                {
                    $array[] = $this->quoteField($val);
                }
                else
                {
                    $array[] = $this->quoteField($key).' '.$val;
                }
            }

            $order = implode(',',$array);
        }

        return !empty($order) ? ' ORDER BY '.$order : '';
    }

    /**
     * 转换显示记录数为SQL格式
     *
     * @param $limit
     * @param int $offSet
     * @return string
     */
    protected function parseLimit($limit, $offSet = 0)
    {
        return $limit ? ' LIMIT '.max(0, $offSet).', '.$limit.' ' : '';
    }

    /**
     * 转换连接语句为SQL格式
     *
     * @param $join
     * @return string
     */
    protected function parseJoin($join)
    {
        $joinStr = '';
        if (!empty($join))
        {
            if (is_array($join))
            {
                foreach ($join as $key => $value)
                {
                    if (false !== stripos($value, 'JOIN'))
                        $joinStr .= ' '.$value;
                    else
                        $joinStr .= ' LEFT JOIN ' .$value;
                }
            }
            else
            {
                $joinStr .= ' LEFT JOIN ' .$join;
            }
        }

        return $joinStr;
    }

    /**
     * 转换分组语句为SQL格式
     *
     * @param $group
     * @return string
     */
    protected function parseGroup($group)
    {
        return !empty($group)? ' GROUP BY '.$group : '';
    }

    /**
     * 转换由聚合函数（SUM, COUNT, MAX, AVG等）运算结果的输出进行限制功能语句为SQL格式
     *
     * @param $having
     * @return string
     */
    protected function parseHaving($having)
    {
        return  !empty($having)?   ' HAVING '.$having : '';
    }

    /**
     * 转换过滤重复记录语句为SQL格式
     *
     * @param $distinct
     * @return string
     */
    protected function parseDistinct($distinct)
    {
        return !empty($distinct)?   ' DISTINCT ' : '';
    }

    protected function parsePrimaryKey(array $primaryKeys = array())
    {
        $sql = '';
        if (!is_array($primaryKeys) || !count($primaryKeys)) return  $sql;
        
        $keyName = $this->quoteField(implode('_', $primaryKeys));
	    $primaryKeys = $this->parseField($primaryKeys);
        
	    $sql = ",\n\tPRIMARY KEY ".$keyName." (" . $primaryKeys . ")";

        return $sql;
    }

    protected function parseKey(array $keys = array())
    {
        $sql = '';
        if (!is_array($keys) || !count($keys)) return $sql;

        foreach ($keys as $key)
	    {
			if (is_array($key))
			{
				$keyName = $this->quoteField(implode('_', $key));
				$key = $this->parseField($key);
			}
			else
			{
				$keyName = $this->quoteField($key);
				$key = $keyName;
			}

			$sql .= ",\n\tKEY {$keyName} (" . $key. ")";
		}

        return $sql;
    }

    protected function parseStructureColumns($columns)
    {
		if (!is_array($columns)) return $columns;
		
        $sql = '';

        foreach ($columns as $column => $attributes)
        {
            if (!is_string($column)) continue;

            $attributes = array_change_key_case($attributes, CASE_UPPER);

		    $sql .= "\n\t".$this->quoteField($column);

			if (array_key_exists('NAME', $attributes))
			{
				$sql .= ' '.$this->quoteField($attributes['NAME']).' ';
			}

			if (array_key_exists('TYPE', $attributes))
			{
				$sql .=  ' '.$attributes['TYPE'];

				if (array_key_exists('CONSTRAINT', $attributes))
				{
					switch ($attributes['TYPE'])
					{
						case 'decimal':
						case 'float':
						case 'numeric':
							$sql .= '('.implode(',', $attributes['CONSTRAINT']).')';
						    break;
						case 'enum':
						case 'set':
							$sql .= '("'.implode('","', $attributes['CONSTRAINT']).'")';
							break;
						default:
							$sql .= '('.$attributes['CONSTRAINT'].')';
					}
				}
			}

			if (array_key_exists('UNSIGNED', $attributes) && $attributes['UNSIGNED'] === TRUE)
			{
				$sql .= ' UNSIGNED';
			}

			if (array_key_exists('DEFAULT', $attributes))
			{
				$sql .= ' DEFAULT \''.$attributes['DEFAULT'].'\'';
			}

			if (array_key_exists('NULL', $attributes) && $attributes['NULL'] === TRUE)
			{
				$sql .= ' NULL';
			}
			else
			{
				$sql .= ' NOT NULL';
			}

			if (array_key_exists('AUTO_INCREMENT', $attributes) && $attributes['AUTO_INCREMENT'] === TRUE)
			{
				$sql .= ' AUTO_INCREMENT';
			}

            $sql .= ',';
        }
        $sql && $sql = substr($sql, 0, strlen($sql)-1);

        return $sql;
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
        $field = trim($field);
        $search = array(' ', ',', '*', '.', '`', '(', ')');
        $value = str_replace($search, '##', $field);

        if( false === strpos($value,'##'))
            $field = '`'.$field.'`';

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
        empty($value) && $value = '';
		if(is_int($value) || is_float($value) || is_bool($value))
		{
			return $value;
		}
		else if(is_null($value))
		{
			return 'NULL';
		}
		else
		{
			return '\'' . mysql_real_escape_string($value) . '\'';
		}
    }
}
