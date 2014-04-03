<?php
class BC_Database_Driver_Oracle extends Database_DriverAbstract
{
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
     * @see Database_DriverAbstract::connect()
     * @abstract
     * @return boolean
     */
    public function connect()
    {
        if (($class = self::isSupport()) === false)
            throw new BC_Exception("The Oracle PECL extension has not been installed or enabled");

        try
        {
            $this->connection = oci_connect($this->getServer(), $this->config['options']);
            $this->selectDB($this->config['database']);
            return true;
        }
        catch (Exception $e)
        {
            //throw new BC_Exception($e);
            return false;
        }

    }

    /**
     * 数据库查询
     *
     * @see Database_DriverAbstract::query()
     * @abstract
     * @param $queryData
     * @return Database_Driver_Mongo_Result
     */
    public function query($queryData)
    {

    }

    /**
     * 转换输出sql语句
     *
     * @see Database_DriverAbstract::toSql()
     * @abstract
     * @param $queryData 同上
     * @return string
     */
    public function toSql($queryData)
    {

    }

    public function selectDB($database)
    {

    }

    public function dropDB()
    {

    }

    public function getServer()
    {

    }

    public function getPrefix()
    {
        return $this->config['prefix'];
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
        return '';
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
        return class_exists('oci8');
    }

    /**
     * 过滤字段，表名
     *
     * @see Database_DriverAbstract::quoteField()
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
     * @see Database_DriverAbstract::quoteValue()
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