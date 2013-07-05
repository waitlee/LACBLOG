<?php
namespace \LAC\core;

class webHttpapplication
{
	private $_conf;

	public function __construct($config)
	{
		$this->_conf = new \LAC\helper\arrayMap($config);
		
	}
}