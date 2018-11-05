<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Helper\DataType;
use Amsify42\TypeStruct\Core\Structure;
use stdClass;

class Struct
{
	/**
	 * Contains the object with key value pair
	 * @var object
	 */
	protected $data;

	/**
	 * Contains the structure with key value pair
	 * @var object
	 */
	protected $structure;

	/**
	 * Decides whether to send single type error or of complete object
	 * @var boolean
	 */
	private $validateFull;

	/**
	 * Response data after validations
	 * @var [type]
	 */
	private $response 	= [];

	/**
	 * Status of object validation
	 * @var bool
	 */
	private $isValid 	= false;

	/**
	 * Initializing typestruct by validating and assigning values
	 * @param stdClass     $data
	 * @param stdClass     $structure
	 * @param bool|boolean $validateFull
	 */
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

	/**
	 * Gets the value from object dictionary
	 * @param  string $name
	 * @return mixed
	 */
	function __get($name)
	{
		if(isset($this->data->{$name})) {
			return $this->data->{$name};
		}
	}

	/**
	 * Validate the type of key and assign value
	 * @param string $name
	 * @param mixed $value
	 */
	function __set($name, $value)
	{
		if(isset($this->data->{$name})) {
			$this->data->{$name} = DataType::assign($name, $this->data->{$name}, $value);	
		} else {
			throw new \RuntimeException($name." is not the property of struct:".get_called_class());
		}
	}

	/**
	 * Get complete object data
	 * @return stdClass
	 */
	public function getData(): stdClass
	{
		return $this->data;
	}

	/**
	 * Get complete structure data
	 * @return stdClass
	 */
	public function getStructure(): stdClass
	{
		return $this->structure;
	}

	/**
	 * Checks if validation type is full
	 * @return boolean
	 */
	public function isValidateFull(): bool
	{
		return $this->validateFull;
	}

	/**
	 * returns the response
	 * @return boolean
	 */
	public function getResponse(): array
	{
		return $this->response;
	}
}