<?php
namespace LAC\core;

use \LAC\core\webHttpapplication;
use \LAC\LAC;

class Route
{
	private $_routers;
	private $_pathInfo;

	public function __construct()
	{
		$this->_pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : NULL;
		$this->_routers = $this->_parseUrl();
	}

	public function getRoute()
	{
		LAC::debug($this->_routers);
		return array($this->_routers['controller'], $this->_routers['action']);
	}

	private function _parseUrl()
	{
		if ($this->_pathInfo == NULL && !isset($_GET['c'])) {
			$routers = array(
						'controller' => webHttpapplication::defaultController, 
						'action' => webHttpapplication::defaultAction,
					);
		} elseif ($this->_pathInfo != NULL) {
			$routers = $this->_parsePathInfo();
		} else {
			$routers = array(
						'controller' => isset($_GET['c']) ? $_GET['c'] : webHttpapplication::defaultController,
						'action' => isset($_GET['a']) ? $_GET['a'] : webHttpapplication::defaultAction,
					);
		}
		return $routers;
	}

	private function _parsePathInfo()
	{
		$pathInfo = explode("/", trim($this->_pathInfo, '/'));
		$num = count($pathInfo);
		switch ($num) {
			case 1:
				$array = array(
						'controller' => array_shift($pathInfo),
						'action' => webHttpapplication::defaultAction,
					);
				break;

			case 2:
				$array = array(
						'controller' => array_shift($pathInfo),
						'action' => array_shift($pathInfo),
					);
				break;
			
			default:
				$array = array(
						'controller' => array_shift($pathInfo),
						'action' => array_shift($pathInfo),
					);
				$this->_parseParams($pathInfo);
				break;
		}

		return $array;
	}

	private function _parseParams($params)
	{
		$count = count($params);
		for ($i=0; $i < $params; $i += 2) { 
			if (empty($params[$i])) {
				break;
			} else {
				$_REQUEST[$params[$i]] = $_GET[$params[$i]] = !empty($params[$i+1]) ? $params[$i+1] : NULL;
			}
		}
	}
}