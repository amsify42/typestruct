<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\TypeStruct;
use ReflectionClass;

class Validator
{
	public $typeStruct;

	protected $baseNameSpace;
	protected $validateFull = true;

	private $classLoaded = [];
	protected $activeMethod;

	function __construct()
	{
		$this->typeStruct = new TypeStruct();
	}

	function __call($method, $arguments)
	{
		if(is_callable([$this, $method]) && !$this instanceof Amsify42\TypeStruct\Core\Validator) {
			$calledClass 	= get_called_class();
			$reflection 	= new ReflectionClass($calledClass);
			$rMethod 		= $reflection->getMethod($method);
			if($rMethod->getNumberOfParameters()> 0) {
				foreach($rMethod->getParameters() as $pKey => $param) {
					preg_match('/>(.*?)\$/ims', $param->__toString(), $matches);
					$struct = trim($matches[1]);
					if($struct) {
						$arguments[$pKey] = $this->getTypeStruct($struct, $arguments[$pKey]);
					}
				}
			}
			if($rMethod->hasReturnType()) {
				$this->activeStruct = trim($rMethod->getReturnType()->__toString());
			}
			return call_user_func_array([$this, $method], $arguments);
		}
	}

	public function setBaseNamespace($baseNameSpace)
	{
		$this->baseNameSpace = $baseNameSpace;
	}

	private function getTypeStruct($struct, $data)
	{
		if(!$this->baseNameSpace || strpos($struct, $this->baseNameSpace) !== false) {
			$this->typeStruct->setClass($struct);
			$this->typeStruct->setValidateFull($this->validateFull);
			$response 	= $this->typeStruct->validate($data);
			if($response['isValid']) {
				return $this->typeStruct->getTypeStruct();
			} else {
				$message = "Structure must be of type '{$struct}'\n";
				$message .= "\nErrors:\n";
				$message .= implode(", ", $response['messages']);
				throw new \RuntimeException($message);
			}
		}
		return $data;
	}

	protected function validateReturn($data)
	{
		if($this->activeStruct) {
			$struct 				= $this->activeStruct;
			$this->activeStruct 	= NULL;
			return $this->getTypeStruct($struct, $data);
		}
		return $data;
	}
}