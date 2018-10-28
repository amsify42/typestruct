<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\Helper\DataType;

class Struct
{
	protected $data;

	function __construct($data)
	{
		$this->data = DataType::childToStruct($data);
	}

	function __get($name)
	{
		if(isset($this->data->{$name})) {
			return $this->data->{$name};
		}
	}

	function __set($name, $value)
	{
		if(isset($this->data->{$name})) {
			$this->data->{$name} = DataType::assign($name, $this->data->{$name}, $value);	
		} else {
			throw new \RuntimeException($name." is not the property of struct:".get_called_class());
		}
	}
}