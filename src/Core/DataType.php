<?php

namespace Amsify42\TypeStruct\Core;

class DataType
{
	protected $value;
	
	function __call($name, $arguments)
	{
		if($name == 'val' || $name == 'value') {
			return $this->value;
		}
	}

	public function getType()
	{
		return gettype($this->value);
	}
}