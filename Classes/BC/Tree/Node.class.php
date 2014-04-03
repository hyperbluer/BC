<?php
/**
 * Node.class.php 树节点类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Tree_Node
{

    /**
     * 子节点集合
     *
     * @var array
     */
	public $children;

    /**
     * 构造函数
     *
     * @param $data
     */
	public function __construct($data)
    {
		$this->children = array();
	}

    /**
     * 魔术方法，创建node类成员变量
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * 增加子节点
     *
     * @param $node
     * @return void
     */
	public function addChild($node)
    {
		$this->children[] = $node;
	}

    /**
     * 删除子节点
     *
     * @param $i
     * @return void
     */
	public function deleteChild($i)
    {
		if(isset($this->children[$i]))
        {
			unset($this->children[$i]);
		}
	}
}