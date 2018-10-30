<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Helper\DataType;
use Amsify42\TypeStruct\Core\Structure;
use stdClass;

class Struct
{
	protected $structure;
	protected $data;

	function __construct(stdClass $data, stdClass $structure)
	{
		$struct 	= new Structure($structure);
		$response 	= $struct->validate($data);
		if($response['isValid']) {
			$this->data 		= DataType::childToStruct($data, $structure);
			$this->structure 	= $structure;
		} else {
			$message = "Structure must be of type '".get_called_class()."'\n";
			$message .= "\nErrors:\n";
			$message .= implode(", ", $response['messages']);
			throw new \RuntimeException($message);
		}
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

	public function getData()
	{
		return $this->data;
	}

	public function getStructure()
	{
		return $this->structure;
	}
}