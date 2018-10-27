<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\TypeStruct;
use ReflectionClass;

class Validator
{
	public $typeInterface;

	protected $baseNameSpace;
	protected $validateFull = true;

	private $classLoaded = [];
	protected $activeMethod;

	function __construct()
	{
		$this->typeInterface = new TypeStruct();
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
					$interface = trim($matches[1]);
					if($interface) {
						$arguments[$pKey] = $this->getInterface($interface, $arguments[$pKey]);
					}
				}
			}
			if($rMethod->hasReturnType()) {
				$this->activeInterface = trim($rMethod->getReturnType()->__toString());
			}
			return call_user_func_array([$this, $method], $arguments);
		}
	}

	public function setBaseNamespace($baseNameSpace)
	{
		$this->baseNameSpace = $baseNameSpace;
	}

	private function getInterface($interface, $data)
	{
		if(!$this->baseNameSpace || strpos($interface, $this->baseNameSpace) !== false) {
			$this->typeInterface->setClass($interface);
			$this->typeInterface->setValidateFull($this->validateFull);
			$response 	= $this->typeInterface->validate($data);
			if($response['isValid']) {
				return $this->typeInterface->getInterface();
			} else {
				$message = "Structure must be of type '{$interface}'\n";
				$message .= "\nErrors:\n";
				$message .= implode(", ", $response['messages']);
				throw new \RuntimeException($message);
			}
		}
		return $data;
	}

	protected function validateReturn($data)
	{
		if($this->activeInterface) {
			$interface 				= $this->activeInterface;
			$this->activeInterface 	= NULL;
			return $this->getInterface($interface, $data);
		}
		return $data;
	}
}