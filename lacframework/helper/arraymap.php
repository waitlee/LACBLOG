<?php
namespace LAC\helper;

use \ArrayObject;
use \Exception;

class ArrayMap extends ArrayObject
{
	public function __construct($array)
	{
		foreach ($array as &$value) {
			if (is_array($value) && isset($value)) {
				$value = new self($value);
			}
		}
		parent::__construct($array);
	}

	public function __get($index)
	{
		if ($this->offsetExists($index)) {
			return $this->offsetGet($index);
		} else {
			throw new Exception("undefind in arrayMap");
		}
	}

	public function __isset($index)
	{
		if ($this->offsetExists($index)) {
			return true;
		} else {
			return false;
		}
	}	
}