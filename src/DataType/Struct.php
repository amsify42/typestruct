<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Helper\DataType;
use Amsify42\TypeStruct\Core\Structure;
use stdClass;

class Struct
{
	/**
	 * Contains the object with key value pair
	 * @var stdClass
	 */
	protected $data;

	/**
	 * Contains the structure with key value pair
	 * @var string|stdClass
	 */
	protected $structure;

	/**
	 * Decides whether to send single type error or of complete object
	 * @var boolean
	 */
	private $validateFull;

	/**
	 * Response data after validations
	 * @var array
	 */
	private $response 	= [];

	/**
	 * Initializing typestruct by validating and assigning values
	 * @param stdClass|array  	$data
	 * @param stdClass  		$structure
	 * @param boolean 			$validateFull
	 */
	function __construct($data, stdClass $structure = NULL, bool $validateFull = false)
	{
		if(!is_array($data) && !$data instanceof stdClass) {
			throw new \RuntimeException('TypeStruct Error: Data must be of type stdClass or array');
		}
		if($structure) $this->structure = $structure;
		$data 				= is_array($data)? arrayToObject($data, $this->structure): $data;
		$this->validateFull = $validateFull;
		$struct 			= new Structure($this->structure);
		$struct->setValidateFull($this->validateFull);
		$this->response 	= $struct->validate($data);
		if($this->response['isValid']) {
			$this->data 	= DataType::childToStruct($data, $this->structure, false, $this->validateFull);
		} else {
			if(!$this->validateFull) {
				$message = "Structure must be of type '".get_called_class()."'\n";
				$message .= "\nError: ".$this->response['message'];
				throw new \RuntimeException($message);
			}
		}
	}

	/**
	 * Gets the value from object dictionary
	 * @param  string $name
	 * @return mixed
	 */
	function __get($name)
	{
		$this->checkValid();
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
		$this->checkValid();
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

	/**
	 * Checks if validated
	 * @return boolean
	 */
	public function isValid(): bool
	{
		return (isset($this->response['isValid']) && $this->response['isValid']);
	}

	/**
	 * Check if validated else throw error
	 */
	private function checkValid()
	{
		if(!$this->isValid()) {
			throw new \RuntimeException("TypeStruct Error: '".get_called_class()."' not validated to perform further operations");	
		}
	}
}