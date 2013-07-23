<?php
namespace LAC\core;

use \LAC\helper\ArrayMap;
use \LAC\core\Route;
use \LAC\LAC;
use \Exception;

class webHttpapplication
{
	private $_conf;
	private $_route;
	
	CONST defaultController = "welcome";
	CONST defaultAction = "index";

	public function __construct($config)
	{
		$this->_conf = $this->_configReader($config);
		$this->_route = new Route();
	}

	public function conf()
	{
		return $this->_conf;
	}

	public function getConfig()
	{
		return $this->_conf;
	}
	
	private function _configReader($config)
	{
		if (file_exists($config)) {
			$c = require $config;
			return new ArrayMap($c);
		} else {
			throw new Exception ("config file not found");
		}
	}

	public function _runAction()
	{
		$relCA = $this->_getCA();

		$c = new $relCA['c']();
		$c->run($relCA['a']);
	}

	private function _getCA()
	{
		list($controller, $action) = $this->_route->getRoute();
		$controllerPath = $this->_conf->path->controllerPath;

		$relConroller = $controllerPath . DS . ucfirst($controller);
		return array(
				'c' => $relConroller,
				'a' => $action . 'Action',
			);
	}

	public function run()
	{
		try {
			$this->_runAction();
		} catch (Exception $e) {
			// var_dump($e->getTrace());
			var_dump($e->getMessage());
		}
	}

}