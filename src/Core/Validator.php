<?php

namespace Amsify42\TypeStruct\Core;

use Amsify42\TypeStruct\TypeStruct;
use Amsify42\TypeStruct\Core\Structure;
use ReflectionClass;
use stdClass;

class Validator
{
	protected $baseNameSpace;
	protected $validateFull = true;
	protected $activeMethod;

	private $classLoaded 	= [];
	private $response 		= ['isValid' => true, 'messages' => []];

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
			$typeStruct->setClass($struct);
			$typeStruct->setValidateFull($this->validateFull);
			return $typeStruct->getTypeStruct();
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