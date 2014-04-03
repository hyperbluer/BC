<?php
/**
 * Hook.class.php 钩子类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-07-10
 */
defined('IN_BC') or exit("Access Denied!");

class BC_Hook
{
    /**
     * HOOK实例
     *
     * @var null
     */
	protected static $instance = null;

    /**
     * 系统加载的所有hook
     *
     * @var array
     */
	private static $actions = array();

    /**
     * 系统执行所有加载hook返回结果集合
     *
     * @var array
     */
	private static $returns = array();

    /**
     * 单例模式
     *
     * @static
     * @return null
     */
	public static function instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new BC_Hook();
		}

		return self::$instance;
	}

    /**
     * 判断动作钩子是否存在
     *
     * @param $name
     * @return bool
     */
    public function hasAction($name)
    {
        if ( isset(self::$actions[$name]) )
        {
            return true;
        }

        return false;
    }

    /**
     * 注册动作钩子
     *
     * @param $name
     * @param $function 数组格式支持两种，例如array('className', 'method')、 array($classObject, 'method')
     * @param array $hookParams
     * @return void
     */
	public function addAction($name, $function, array $hookParams = array())
	{
		if(!isset(self::$actions[$name]))
		{
			self::$actions[$name] = array();
		}

		self::$actions[$name][] = array(
			'function' => $function,
			'hookParams' => $hookParams
		);
	}

    /**
     * 从配置文件configs/hook.php批量注册动作钩子
     *
     * @param $hook
     * @return void
     */
	public function addActionsFromConfig($hook)
	{
		$actions = BC::config('hook/'.$hook);

		if($actions)
		{
			foreach ($actions as $action)
			{
				$this->addAction(
					$action['name'],
					$action['function'],
					( isset($action['params']) ? $action['params'] : array() )
				);
			}
		}
	}

    /**
     * 触发动作钩子
     *
     * @throws BC_Exception
     * @param $name
     * @param array $params
     * @return void
     */
	public function doAction($name, array $params = array())
	{
		if (!$this->hasAction($name)) return false;
		
		$actions = self::$actions[$name];
		
		foreach ($actions as $k => $v)
		{
			$function = $v['function'];
			$hookParams = array_merge($v['hookParams'], $params);
			
			if (is_array($function)) //function函数
			{
				$className = $function[0];
				$method = $function[1];

				if (is_object($className))
				{
					$class = $className;
				}
				else
				{
					if(!class_exists($className))
					{
						throw new BC_Exception('The class '.$className.' does not exist');
					}

					$class = new $className;
				}

				if(!method_exists($class, $method))
				{
					throw new BC_Exception('The method '.$method.' does not exist in the class '.$className);
				}

				$function = array($class, $method);
			}
			else //类方法
			{
				if(!function_exists($function))
				{
					throw new BC_Exception('The function '.$function.' does not exist ');
				}
			}

			self::$returns[$name][] = call_user_func_array($function, $hookParams);
		}

	}

    /**
     * 批量触发动作钩子
     * 
     * @param $name
     * @param array $hookParams
     * @return
     */
	public function doActions($name, array $hookParams = array())
	{
		if( !isset(self::$actions[$name]) or !is_array($hookParams) )
		{
			return;
		}

		foreach (self::$actions[$name] as $action)
		{
			$action['params'] = array_merge($action['params'], $hookParams);

			$this->doAction($name, $action['params']);
		}
	}

    /**
     * 移除已注册的动作钩子
     *
     * @param $name
     * @param null $priority
     * @return bool
     */
    public function removeAction($name, $priority = null)
    {
        if ( !isset(self::$actions[$name]) )
        {
            return true;
        }

        unset(self::$actions[$name]);

        return true;
    }

    /**
     * 返回触发动作钩子执行的结果
     *
     * @param $name
     * @return null
     */
	public function getReturns($name)
	{
		return isset(self::$returns[$name]) ? self::$returns[$name] : array();
	}
}