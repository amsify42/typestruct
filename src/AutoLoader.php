<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\TypeStruct;

class AutoLoader
{	
	private $typeStruct;
	private $baseNameSpace;
	//private $callback;
	private $validateFull 	= true;

	function __construct()
	{
		$this->typeStruct = new TypeStruct();
	}

	public function setBaseNamespace(string $baseNameSpace)
	{
		$this->baseNameSpace = $baseNameSpace;
	}

	public function setValidateFull(bool $isFull)
	{
		$this->validateFull = $isFull;
	}

	public function setCustom(callable $callback)
	{
		$this->callback = $callback;
	}

	public function register()
	{
		$func = ($this->callback)? $this->callback: [$this, 'autoload'];
		spl_autoload_register([$this, 'autoload'], true, true);
	}

	public function autoload($class)
	{
		$actualPath = NULL;
		if(!$this->baseNameSpace || strpos($class, $this->baseNameSpace) !== false) {
			if($this->callback) {
				$path = $this->callback->call($this, $class);
				if($path) {
					$this->typeStruct->setPath($path);
					$actualPath = $this->typeStruct->getActualPath();
				}
			} else {
				$this->typeStruct->setClass($class);
				$this->typeStruct->setValidateFull($this->validateFull);
				$actualPath = $this->typeStruct->getActualPath();
			}
			if($actualPath) {
				require_once $actualPath;
			}
		}
	}
}