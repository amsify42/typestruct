<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\Helper\DataType as DtType;

class DataType
{
	/**
	 * value of variable
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * Get actual value of data when val|value is called or call pre defined functions
	 * @param  string 	$name
	 * @param  array 	$arguments
	 * @return mixed
	 */
	function __call($name, $arguments)
	{
		if($name == 'val' || $name == 'value') {
			return $this->value;
		} else {
			if(function_exists($name)) {
				$arguments[] = $this->value;
				return DtType::getValue(call_user_func_array($name, $arguments));
			}
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