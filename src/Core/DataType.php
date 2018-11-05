<?php

namespace Amsify42\TypeStruct\Core;

class DataType
{
	/**
	 * value of variable
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * Get actual value of data when val|value is called
	 * @param  string 	$name
	 * @param  array 	$arguments
	 * @return mixed
	 */
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