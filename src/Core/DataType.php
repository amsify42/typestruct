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

	/**
	 * Get Type of value
	 * @return string
	 */
	public function getType(): string
	{
		return gettype($this->value);
	}
}