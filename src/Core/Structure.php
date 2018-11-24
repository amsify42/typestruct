<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\Helper as Helper;
use stdClass;

class Structure
{
	/**
	 * Structure of typestruct file as object
	 * @var stdClass
	 */
	private $structure;

	/**
	 * Decides whether to send single type error or of complete object
	 * @var boolean
	 */
	private $validateFull 	= false;

	/**
	 * For locating key path with token between them
	 * @var string
	 */
	private $token 			= '->';

	/**
	 * Response data after validations
	 * @var array
	 */
	private $response 		= ['isValid' => true, 'messages' => []];

	function __construct(stdClass $structure)
	{
		$this->structure = $structure;
	}

	/**
	 * Set Full Validation for full error messages needs to be returned
	 * @param bool $isFull
	 */
	public function setValidateFull(bool $isFull): void
	{
		$this->validateFull = $isFull;
	}

	/**
	 * Set Token for object key path
	 * @param string $token
	 */
	public function setToken(string $token = '->'): void
	{
		$this->token = $token;
	} 

	/**
	 * Validates the key value pair of object
	 * @param  stdClass      $data
	 * @param  string|null   $path
	 * @param  stdClass|null $dictionary
	 * @return array
	 */
	private function iterateDictionary(stdClass $data, string $path = null, stdClass $dictionary = null): array
	{
		$structure 	= ($dictionary)? $dictionary: $this->structure;
		$result 	= ['isValid' => true, 'message' => '', 'path' => ''];
		foreach($structure as $name => $type) {
			if(!isset($data->{$name})) {
				if(is_array($type) || ($type != 'any' && strtolower($type) != 'null')) {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' is not defined';
					$result['path']    .= $name.$this->token;
					if($this->validateFull) {
						$this->response['messages'][pathKey($path, $name, $this->token)] = $result['message'];
					} else {
						break;
					}
				}
			} else {
				if($data->{$name} instanceof \Amsify42\TypeStruct\DataType\Struct) {
					continue;
				} else if(is_object($type)) {
					$childPath 	= ($path)? $path.$this->token.$name: $name;
					$validated 	= $this->iterateDictionary($data->{$name}, $childPath, $type);
					if(!$validated['isValid']) {
						$result['isValid'] 	= false;
						$result['message'] 	= $validated['message'];
						$result['path']    .= $name.$this->token.$validated['path'];
						if(!$this->validateFull) break;
					}
				} else if($type['type'] == 'array') {
					$typeResult = Helper\DataType::checkArrayType($name, $data->{$name}, $type);
					if(!$typeResult['isValid']) {
						$result['isValid'] 	= false;
						$result['message'] 	= $typeResult['message'];
						$result['path']    .= $name.$this->token;
						if($this->validateFull) {
							$this->response['messages'][pathKey($path, $name, $this->token)] = $result['message'];
						} else {
							break;
						}
					} else {
						$data->{$name} = (!empty($typeResult['value']))? $typeResult['value']: $data->{$name};
						$data->{$name} = Helper\DataType::getInstance($data->{$name}, $type);
					}
				} else {
					$typeResult = Helper\DataType::checkType($name, $data->{$name}, $type);
					if(!$typeResult['isValid']) {
						$result['isValid'] 	= false;
						$result['message'] 	= $typeResult['message'];
						$result['path']    .= $name.$this->token;
						if($this->validateFull) {
							$this->response['messages'][pathKey($path, $name, $this->token)] = $result['message'];
						} else {
							break;
						}
					 } else {
						$data->{$name} = Helper\DataType::getInstance($data->{$name}, ['type' => $type]);
					 }
				}
			}			
		}
		$result['path'] = rtrim($result['path'], $this->token);
		return $result;
	}

	/**
	 * Validate the given object with types info
	 * @param  stdClass $data [description]
	 * @return array
	 */
	public function validate(stdClass $data): array
	{
		$response = $this->iterateDictionary($data);
		if($this->validateFull && sizeof($this->response['messages'])> 0) {
			$this->response['isValid'] = false;
			return $this->response;
		} else {
			return $response;
		}
	}
}