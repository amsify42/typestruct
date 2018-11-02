<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Helper\DataType;
use Amsify42\TypeStruct\Core\Structure;
use stdClass;

class Struct
{
	protected $data;
	protected $structure;

	private $validateFull;
	private $response 	= [];
	private $isValid 	= false;

	function __construct(stdClass $data, stdClass $structure, bool $validateFull = true)
	{
		$this->validateFull = $validateFull;
		$struct 			= new Structure($structure);
		$struct->setValidateFull($this->validateFull);
		$this->response 	= $struct->validate($data);
		if($this->response['isValid']) {
			$this->data 		= DataType::childToStruct($data, $structure, false, $this->validateFull);
			$this->structure 	= $structure;
			$this->isValid 		= true;
		} else {
			// $message = "Structure must be of type '".get_called_class()."'\n";
			// $message .= "\nErrors:\n";
			// $message .= implode(", ", $response['messages']);
			// throw new \RuntimeException($message);
			$this->isValid 		= false;
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

	public function isValidateFull()
	{
		return $this->validateFull;
	}

	public function getResponse()
	{
		return $this->response;
	}
}