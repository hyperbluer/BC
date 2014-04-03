<?php
/**
 * Redis.class.php Redis缓存类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-04
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Cache_Handler_Redis extends Cache_CacheAbstract
{
    /**
     * 缓存配置
     * @var array
     */
    private $config;

    /**
     * Redis实例化对象
     *
     * @var \Redis
     */
    private $handle;

    /**
     * 构造函数
     *
     * @throws BC_Exception
     * @param $config
     */
    public function __construct($config)
    {
        if ( !self::isSupport())
        {
            throw new BC_Exception('Php Redis extension is not available.');
        }

        $this->config = $config;
        !isset($this->config['host']) && $this->config['host'] = 'localhost';
        !isset($this->config['port']) && $this->config['port'] = 6379;
        !isset($this->config['timeout']) && $this->config['timeout'] = 0;

        $this->handle = new Redis();

        try
        {
            $this->handle->connect($this->config['host'], $this->config['port'], $this->config['timeout']);
        }
        catch (RedisException $e)
        {
            throw new BC_Exception('Couldn\'t connect to redis!');
        }
    }

    /**
     * 键值对 写入操作
     *
     * @param $key  键名
     * @param $value    值
     * @param int $expire   过期时间
     * @return bool
     */
    public function set($key, $value, $expire = 0)
    {
        $args_num = func_num_args();
		if(is_array($key) && $args_num == 1)
		{
			$values = $key;
			foreach($values AS $_key => $_val)
			{
				$this->handle->set($_key, $_val);
			}
		}
		else
		{
			return $this->handle->set($key, $value);
		}

		return true;
    }

    /**
     * 键值对 读取操作
     *
     * @param $key  键名
     * @return bool
     */
    public function get($key)
    {
        if(is_array($key))
		{
			return $this->handle->mget($key);
		}
		elseif ($this->handle->exists($key))
		{
			return $this->handle->get($key);
		}
		else
		{
			return null;
		}
    }

    /**
     * 键值对 删除操作
     *
     * @param $key  键名
     * @return null|int  返回已经删除key的个数
     */
    public function delete($key)
    {
        if (empty($key)) return null;

		return $this->handle->delete($key);
    }

    /**
     * 清空数据库
     *
     * @param string $cacheType db 清空当前数据库 all 清空所有数据库（慎用）
     * @return bool
     */
    public function flush($cacheType = 'db')
    {
        if ($cacheType == 'all')
            return $this->handle->flushAll();
        else
            return $this->handle->flushDb();
    }
    
    /**
     * List类型-链表进栈(插入)操作
     *
     * @param $key  链表名
     * @param $value    插入的值
     * @param string $direction 头/尾插入
     * @param bool $overlay key重名是否覆盖值， true 覆盖 false 不覆盖
     * @return null|int 返回插入的数据位置
     */
	public function listPush($key, $value, $direction = 'r', $overlay = true)
	{
		if (empty($key)) return null;
		$func = $direction == 'r' ? 'rPush' : 'lPush';
		$func = $overlay ? $func : $func.'x';
		return $this->handle->$func($key, $value);
	}

    /**
     * List类型-链表出栈(删除)操作
     *
     * @param $key  链表名
     * @param string $direction 头/尾开始删除
     * @return null|int 返回插入的数据位置
     */
	public function listPop($key, $direction = 'r')
	{
		if (empty($key)) return null;
		$func = $direction == 'r' ? 'rPop' : 'lPop';
		return $this->handle->$func($key);
	}

    /**
     * List类型-返回链表的元素个数
     *
     * @param $key 链表名
     * @return null|int
     */
	public function listSize($key)
	{
		if (empty($key)) return null;
		return $this->handle->lSize($key);
	}

    /**
     * List类型-返回链表中index位置的元素
     *
     * @param $key  链表名
     * @param $index    索引号
     * @return null|string
     */
	public function listGet($key, $index)
	{
		if (empty($key) || !is_numeric($index)) return null;
		return $this->handle->lGet($key, $index);
	}

    /**
     * List类型-给链表中index位置的元素赋值
     * 
     * @param $key  链表名
     * @param $index 索引号
     * @param $value 赋值
     * @return null|string
     */
	public function listSet($key, $index, $value)
	{
		if (empty($key) || !is_numeric($index)) return null;
		return $this->handle->lSet($key, $index, $value);
	}

    /**
     * List类型-返回链表start至end之间的元素
     * 
     * @param $key  链表名
     * @param int $start    开始索引号 默认从0即第一条记录开始
     * @param $end  结束索引号(-1返回所有, 负值表示从后面开始计算)
     * @return null|array
     */
	public function listRange($key, $start = 0, $end = -1)
	{
		if (empty($key)) return null;
		return $this->handle->lRange($key, $start, $end);
	}

    /**
     * List类型-删除链表指定的索引
     *
     * @param $key  链表名
     * @param $index    索引号
     * @param int $length   删除的元素数量
     * @return bool
     */
	public function listDeleteIndex($key, $index, $length = 69)
	{
		$value = '';
		for (; $length > 0; --$length) {
			$value .= chr(rand(32, 126));
		}

		$this->handle->lSet($key, $index, $value);
		return $this->handle->lRem($key, $value, 1);
	}

    /**
     * 获取所有匹配的key列表，默认匹配所有即*
     *
     * @param string $pattern 正则
     * @return  null|array
     */
	public function getKeys($pattern = '*')
	{

		$keys = $this->handle->keys($pattern);
		return $keys;
	}

    /**
     * 检测运行环境是否支持此缓存扩展
     *
     * @static
     * @return bool
     */
    public static function isSupport()
    {
        return extension_loaded('redis');
    }
}