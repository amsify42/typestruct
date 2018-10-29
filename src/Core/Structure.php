<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\Helper as Helper;
use stdClass;

class Structure
{
	private $structure;
	private $validateFull 	= true;
	private $token 			= '->';
	private $response 		= ['isValid' => true, 'messages' => []];

	function __construct(stdClass $structure)
	{
		$this->structure = $structure;
	}

	public function setValidateFull(boolean $isFull)
	{
		$this->validateFull = $isFull;
	}

	public function setToken(string $token = '->')
	{
		$this->token = $token;
	} 

	private function iterateDictionary(stdClass $data, string $path = null, stdClass $dictionary = null)
	{
		$structure 	= ($dictionary)? $dictionary: $this->structure;
		$result 	= ['isValid' => true, 'message' => '', 'path' => ''];
		foreach($structure as $name => $type) {
			if(!isset($data->{$name}) && $type != 'any' && strtolower($type) != 'null') {
				$result['isValid'] 	= false;
				$result['message'] 	= $name.' is not defined';
				$result['path']    .= $name.$this->token;
				if($this->validateFull) {
					$this->response['messages'][pathKey($path, $name, $this->token)] = $result['message'];
				} else {
					break;
				}
			} else {
				if(is_object($type)) {
					$childPath 	= ($path)? $path.$this->token.$name: $name;
					$validated 	= $this->iterateDictionary($data->{$name}, $childPath, $type);
					if(!$validated['isValid']) {
						$result['isValid'] 	= false;
						$result['message'] 	= $validated['message'];
						$result['path']    .= $name.$this->token.$validated['path'];
						if(!$this->validateFull) break;
					}
				} else if(is_array($type)) {
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
						$data->{$name} = Helper\DataType::getInstance($data->{$name}, $type);
					 }
				}
			}			
		}
		$result['path'] = rtrim($result['path'], $this->token);
		return $result;
	}

	public function validate(stdClass $data)
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