<?php
namespace LAC\core;

use \LAC\core\View;
use \Exception;

class BaseController
{
	private $_view;

	final function __construct()
	{
		$this->_view = View::getInstance(get_class($this));
	}

	protected function beforeAction($action)
	{
		return true;
	}

	protected function afterAction($action)
	{
	}

	protected function assign($key, $value)
	{
		$this->_view->assign($key, $value);
	}

	protected function render($viewPage = NULL)
	{
		echo $this->_view->render($viewPage);
	}

	/**
	 * 
	 * @param  [type] $action [description]
	 * @return [type]         [description]
	 */
	public function run($action)
	{
		$this->_actionExist($action);

		$ba = $this->beforeAction($action);
		if ($ba === true) {
			$this->$action();
		} else {
			throw new Exception("Error Processing Request");
		}
	}

	/**
	 * 判断action是否存在
	 * 
	 * @param  string $action  要执行的方法名
	 * 
	 * @return  boolean        
	 */
	private function _actionExist($action)
	{
		if (method_exists($this, $action)) {
			return true;
		} else {
			throw new Exception(sprintf("method '%s' not found in class '%s'", $action, get_class($this)));
		}
	}
}