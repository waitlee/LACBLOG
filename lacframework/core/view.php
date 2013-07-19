<?php
namespace LAC\core;

use \LAC\LAC;
use \Exception;

class View
{
	private $_dir;
	private $_app;
	private $_vars;

	public static function getInstance($dir)
	{
		static $instance = NULL;
		if($instance === NULL)
			$instance = new self($dir);
		return $instance; 
	}

	private function __construct($dir)
	{
		$this->_dir = strtolower($dir);
		$this->_app = LAC::app();
		$this->_vars = array();
	}

	public function assign($key, $value)
	{
		$this->_vars[$key] = $value;
		return $this;
	}

	public function render($viewPage = NULL)
	{
		if ($viewPage === NULL) {
			$viewPage = 'index';
		}

		$viewPagePath = $this->_getViewPrifixPath($viewPage);
		extract($this->_vars);
		ob_start();
		require $viewPagePath;
		return ob_get_clean();
	}

	private function _getViewPrifixPath($viewPage)
	{
		$appPath = LAC::app()->conf()->path->viewPath;
		$appPathInfo = explode('\\' ,trim($appPath, '\\'));

		$prefix = array_shift($appPathInfo);
		$appPrifix = LAC::getPreFix($prefix);
		$relViewPagePath = $appPrifix . array_shift($appPathInfo) . DS . $this->_getDirname() . DS . $viewPage . '.php';
		if (!file_exists($relViewPagePath)) {
			throw new Exception("{$viewPage}" . ".php not found");
		}
		return $relViewPagePath;
	}

	private function _getDirname()
	{
		$dirPath = explode('\\', $this->_dir);
		$dirName = array_pop($dirPath);
		return $dirName;
	}
}