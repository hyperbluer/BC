<?php
/**
 * Tree.class.php 数据集合转换成树状结构类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-05
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Tree
{
    public $rootNode;
    public $idItem = 'id';
    public $parentIdItem = 'parentId';
    public $nameItem = 'name';

	private $dataSet;

    /**
     * 构造函数
     * 
     * @param $data
     */
	public function __construct($data)
    {
        $rootData = array($this->parentIdItem => null, $this->idItem => 0, $this->nameItem => 'root');
		$this->rootNode = new Tree_Node($rootData);
        foreach ($rootData as $k => $v)
        {
            $this->setNodeItem($k, $v, $this->rootNode);
        }
        
		$this->dataSet = $data;
		$this->createTree($this->rootNode);
	}

    /**
     * 设置节点字段值
     *
     * @param $key
     * @param $value
     * @param $node
     * @return void
     */
    public function setNodeItem($key, $value, $node)
    {
        $node->$key = $value;
    }

    /**
     * 递归生成树状结果集
     *
     * @param $node
     * @return void
     */
	private function createTree($node)
    {
		foreach($this->dataSet as $key => $value)
        {
			if($value[$this->parentIdItem] == $node->{$this->idItem})
            {
                $rootNode = new Tree_Node($value);
                foreach ($value as $k => $v)
                {
                    $this->setNodeItem($k, $v, $rootNode);
                }
				$node->addChild($rootNode);
				unset($this->dataSet[$key]);
			}

		}

		foreach($node->children as &$c)
        {
			$this->createTree($c);
		}
	}

    /**
     * 根据id递归获取节点内容
     *
     * @param $id
     * @param null $node
     * @return null|Tree_Node
     */
	public function getNodeById($id, $node = null)
    {
		$node = $node == null ? $this->rootNode : $node;
        
		if($node->{$this->idItem} == $id)
        {
			return $node;
		}
		else
        {
			foreach($node->children as $c)
            {
				$res = &$this->getNodeById($id, $c);
				if($res != null)
                {
					return $res;
				}
			}
		}
	}

    /**
     * 获取树状结构结果集
     *
     * @return Tree_Node
     */
	public function getTree()
    {
		return $this->rootNode;
	}
}