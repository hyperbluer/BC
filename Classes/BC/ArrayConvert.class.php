<?php
/**
 * ArrayConvert.class.php 数组类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-28
 */

defined('IN_BC') or exit("Access Denied!");

class BC_ArrayConvert
{
    /**
     * 将字符串转换为数组
     *
     * @param	string	$string	字符串
     * @return	array	返回数组格式，如果data为空，则返回空数组
     */
    public static function stringToArray($string)
    {
        $_array = array();
        if ($string == '')
            return $_array;
        
        @eval("\$_array = $string;");
        return $_array;
    }

    /**
     * 将数组转换为字符串
     * 
     * @static
     * @param $array 数组
     * @param int $unEscape 是否还原转义，可选参数，默认为1
     * @return string 返回字符串，如果，data为空，则返回空
     */
    public static function arrayToString($array, $unEscape = 1)
    {
        $_string = '';
        if ($array == '')
            return $_string;
        if ($unEscape)
            $array = Filter::unEscape($array);
        
        return Filter::escape(var_export($array, TRUE));
    }

    /**
     * 对象转换为数组
     *
     * @static
     * @param $object 对象
     * @return array
     */
    public static function objectToArray($object)
    {
        $_array = array();
        $_vars = is_object($object) ? get_object_vars($object) : $object;

        foreach ((array) $_vars as $key => $value) {
            $value = (is_array($value) || is_object($value)) ? self::objectToArray($value) : $value;
            $_array[$key] = $value;
        }

        return $_array;
    }

    /**
     * 将一维数组奇偶下标的值组合成key/value，生成新的数组
     * 应用场景：PATHINFO模式下URL参数解析，例如id/1/page/1
     * example:
     * <code>array('a', 'b', 'c', 'd') => array('a'=>'b', 'c'=>'d')</code>
     *
     * @static
     * @param $array
     * @return array
     */
    public static function arrayValueToKey($array)
    {
        $_array = array();
        if(!is_array($array) || !count($array))
            return $_array;

        $chunkArray = array_chunk($array, 2);
        if(count($chunkArray))
        {
            foreach($chunkArray as $value)
            {
                if (!preg_match('/^[a-z]([a-z0-9]*)$/', $value[0]))
                    continue;
                $_array[$value[0]] = isset($value[1]) ? $value[1] : '';
            }
        }

        return $_array;
    }

    /**
     * 将一维数组key转换成value，生成新的数组
     * example:
     * <code>array('a'=>'b', 'c'=>'d') => array('a', 'b', 'c', 'd')</code>
     *
     * @static
     * @param $array
     * @return array
     */
    public static function arrayKeyToValue($array)
    {
        $_array = array();
        if(!is_array($array) || !count($array)) return $_array;

        foreach ($array as $key => $value)
        {
            if (!preg_match('/^[a-z]([a-z0-9]*)$/', $key))
                continue;
            $_array[] = $key;
            $_array[] = $value;
        }

        return $_array;
    }

    /**
     * 数组合并，同时将数组的所有的 KEY 都转换为大写或小写
     *
     * @static
     * @param array $sourceArray
     * @param array $targetArray
     * @param string $case CASE_LOWER 默认值。以小写字母返回数组的键，CASE_UPPER - 以大写字母返回数组的键。
     * @return array
     */
    public static function mergeArray(array $sourceArray, array $targetArray, $case = CASE_LOWER)
	{
        if (!is_array($targetArray) || !count($targetArray)) return $sourceArray;

		$targetArray  = array_change_key_case($targetArray, $case);
		$sourceArray = array_change_key_case($sourceArray, $case);

		return array_merge($sourceArray, $targetArray);
	}

    /**
     * 一般用于将自动生成的数字索引下标改为某一项有序的值
     *
     * example:
     * <code>
     * $array = array(
     *     '0' => array('id' => 1 , 'name' => 'a'),
     *     '1' => array('id' => 2 , 'name' => 'b')
     * )
     * $array = ArrayConvert::resetArrayKey($array);
     * print_r($array);
     * </code>
     * result:
     * <pre>
     * $array = array(
     *     '1' => array('id' => 1 , 'name' => 'a'),
     *     '2' => array('id' => 2 , 'name' => 'b')
     * )
     * </pre>
     *
     * @static
     * @param $array    待转换的数组
     * @param string $key   此项为转换的有序值key名称。若为无序，同值会被替换。
     * @return array
     */
    public static function resetArrayKey($array, $key = 'id')
    {
        $arrayFormat = array();
        if (!is_array($array) || !count($array)) return $arrayFormat;
        foreach ($array as $v)
        {
            if (isset($v[$key]))
                $arrayFormat[$v[$key]] = $v;
            else
               $arrayFormat[] = $v;
        }

        return $arrayFormat;
    }
	
	/**
	 * 二维数组按某个元素的值排序，最多支持2个元素排序。
	 *
	 * @static
     * @param $array    待转换的数组
     * @param string $order   排序的元素key。 格式如：array('key1' => 'asc', 'key2' => 'desc')
     * @return array
	 */
	public static function multiSortArray($array, $order = array())
	{
		if (!is_array($array) || !is_array($order)) return $array;
		$item1 = array();
		
		$keys = array_keys($order);
		
		if (count($keys) < 1) return $array;
		
        foreach ($array as $k => $v)
        {
			if (!isset($v[$keys[0]])) break;
			$item1[$k] = $v[$keys[0]];
			
			if (isset($keys[1])) 
			{
				if (!isset($v[$keys[1]])) break;
				
				$item2[$k] = $v[$keys[1]];
			}
        }

		if (count($item1) != count($array)) return $array;
		
		$sort1 = $order[$keys[0]] == 'asc' ? SORT_ASC : SORT_DESC;
		if (count($keys) == 2)
		{
			$sort2 = $order[$keys[1]] == 'asc' ? SORT_ASC : SORT_DESC;
			array_multisort($item1, $sort1, $item2, $sort2, $array);
		}
		else
		{
			array_multisort($item1, $sort1, $array);
		}
		
		return $array;
	}
}