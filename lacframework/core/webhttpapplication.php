<?php
namespace \LAC\core;

class webHttpapplication
{
	private $_conf;
	
	CONST defaultController = "welcome";
	CONST defaultAction = "index";

	public function __construct($config)
	{
		$this->_conf = $this->_configReader($config);
	}

	private function _configReader($config)
	{
		if (file_exists($config)) {
			
		}
	}

}