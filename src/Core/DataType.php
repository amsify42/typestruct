<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\Helper\DataType as HelperDataType;

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
				if(count($arguments)> 0 && in_array($name, TS_G_FUNCTIONS)) {
					array_unshift($arguments, $this->value);
				} else {
					$arguments[] = $this->value;
				}
				return HelperDataType::getValue(call_user_func_array($name, $arguments));
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