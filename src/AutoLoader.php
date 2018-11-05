<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\TypeStruct;

class AutoLoader
{	
	/**
	 * Instance of type TypeStruct
	 * @var Amsify42\TypeStruct\TypeStruct
	 */
	private $typeStruct;

	/**
	 * Base namespace of all typestruct files
	 * @var string
	 */
	private $baseNameSpace;

	/**
	 * Callback for custom loading files
	 * @var callable
	 */
	private $callback;

	/**
	 * Decides whether to send single type error or of complete object
	 * @var boolean
	 */
	private $validateFull 	= false;

	function __construct()
	{
		$this->typeStruct = new TypeStruct();
	}

	/**
	 * Set base namespace for autoloading typestruct classes
	 * @param string $baseNameSpace
	 */
	public function setBaseNamespace(string $baseNameSpace): void
	{
		$this->baseNameSpace = $baseNameSpace;
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
	 * Set custom autoloading function for indicating typestruct path 
	 * @param callable $callback [description]
	 */
	public function setCustom(callable $callback): void
	{
		$this->callback = $callback;
	}

	/**
	 * Register and prepend the autoloader function
	 * @return void
	 */
	public function register(): void
	{
		$func = ($this->callback)? $this->callback: [$this, 'autoload'];
		spl_autoload_register([$this, 'autoload'], true, true);
	}

	/**
	 * Autoload the class file
	 * @param  string $class
	 * @return void
	 */
	public function autoload(string $class): void
	{
		$actualPath = NULL;
		if(strpos($class, $this->baseNameSpace) !== false) {
			if($this->callback) {
				$path = $this->callback->call($this, $class);
				if($path) {
					$this->typeStruct->setValidateFull($this->validateFull);
					$this->typeStruct->setPath($path);
					$actualPath = $this->typeStruct->getActualPath();
				}
			} else {
				$this->typeStruct->setValidateFull($this->validateFull);
				$this->typeStruct->setClass($class);
				$actualPath = $this->typeStruct->getActualPath();
			}
			if(is_file($actualPath)) {
				require_once $actualPath;
			}
		}
	}
}