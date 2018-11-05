<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\Core\Resource;
use stdClass;

class TypeStruct extends Resource
{
	/**
	 * Reserved data types
	 * @var array
	 */
	private $reservedTypes 	= ['string', 'int', 'float', 'boolean', 'null', 'any']; 

	/**
	 * Array type names
	 * @var array
	 */
	private $arrayTypes 	= ['array', '[]', 'string', 'int', 'float', 'boolean'];

	/**
	 * Original content of typestruct file
	 * @var string
	 */
	private $originalContent;

	/**
	 * Extracted content of typestruct file after removing comments
	 * @var string
	 */
	private $content;

	/**
	 * For locating key path with token between them
	 * @var string
	 */
	private $token 			= '->';

	/**
	 * Decides whether to send single type error or of complete object
	 * @var boolean
	 */
	protected $validateFull = false;

	/**
	 * Structure of typestruct file as object
	 * @var object
	 */
	protected $structure;

	/**
	 * Info of typestruct file
	 * @var array
	 */
	public 	$info 			= [];

	/**
	 * Set Token for object key path
	 * @param string $token
	 */
	public function setToken(string $token = '->'): void
	{
		$this->token = $token;
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
	 * Set class name of typestruct for validating 
	 * @param string $class
	 */
	public function setClass(string $class): void
	{
		$path = $this->getAutoloadPsr4Path($class);
		if($path) {
			$this->info['path'] = $path;
			$this->extractStructure();
		} else {
			throw new \RuntimeException("Class: '".$class."' not found");
		}
	}

	/**
	 * Set path of typestruct for validating
	 * @param string $path
	 */
	public function setPath(string $path): void
	{
		$this->info['path'] = $path;
		$this->extractStructure();
	}

	/**
	 * Get Actual generated Path
	 * @return string
	 */
	public function getActualPath(): string
	{
		return isset($this->gInfo['php'])? $this->gInfo['php']: NULL;
	}

	/**
	 * Extract structure from typestruct file
	 * @return TypeStruct
	 */
	private function extractStructure(): TypeStruct
	{
		$pathInfo 				= pathinfo($this->info['path']);
		$this->info['name'] 	= $pathInfo['filename'];
		//$this->info['path'] 	= $path;
		$this->originalContent 	= file_get_contents($this->info['path']);
		// Removing commented lines
		$this->content 			= preg_replace('/\/\*[\s\S]+?\*\//', '', $this->originalContent);
		$this->content 			= preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $this->content);
		$this->findClassName();
		$this->findFullClassName();
		$this->findUsedNamespaces();
		$this->structure 		= $this->structToObject($this->content);
		$this->generateStruct();
		return $this;
	}

	/**
	 * Find class name of typestruct
	 * @return void
	 */
	private function findClassName(): void
	{
		$fullName = '';
		preg_match('/export typestruct(.*?){/ims', $this->content, $matches);
		if(isset($matches[1])) {
			$this->info['name'] = trim($matches[1]);
		}
	}

	/**
	 * Find full class name of typestruct
	 * @return void
	 */
	private function findFullClassName(): void
	{
		$fullName = '';
		preg_match('/namespace(.*?);/ims', $this->content, $matches);
		if(isset($matches[1])) {
			$this->info['namespace'] = trim($matches[1]);
			$fullName = $this->info['namespace']."\\".trim($this->info['name']);
		} else {
			$fullName = $this->info['name'];
		}
		$this->info['full_name'] = $fullName;
	}

	/**
	 * Find used namespaces of typestruct
	 * @return void
	 */
	private function findUsedNamespaces(): void
	{
		$classes = [];
		preg_match_all('/use(.*?);/ims', $this->content, $matches);
		if(isset($matches[1])) {
			$classes = array_map('trim', $matches[1]);
		}
		$this->info['used_classes'] = $classes;
	}

	/**
	 * Get Structure of typestruct file
	 * @return object
	 */
	public function getStructure(): object
	{
		return $this->structure;
	}

	/**
	 * Convert structure to json
	 * @return jsonString
	 */
	public function toJson(): string
	{
		return json_encode($this->structure);
	}
	
	/**
	 * Convert structure to object
	 * @param  string $structString [description]
	 * @return object
	 */
	public function structToObject(string $structString): object
	{
		$structure 	= new stdClass();
		$pairs 		= $this->extractDictionary($structString);
		foreach($pairs as $pair) {
			$subPairs = $this->extractDictionary($pair);
			if(sizeof($subPairs) > 0) {
				foreach($subPairs as $subPair) {
					$pair = str_replace($subPair, '', $pair);
				}
			}
			$elements 		= explode(',', $pair);
			$subPairIndex 	= 0;
			foreach($elements as $element) {
				$elementArray = explode(':', trim($element));
				if(sizeof($elementArray)> 1) {
					if(trim($elementArray[1]) == '{}') {
						$structure->{trim($elementArray[0])} = isset($subPairs[$subPairIndex])? $this->structToObject('{'.$subPairs[$subPairIndex].'}'): 'NULL';
						$subPairIndex++;
					} else {
						$result = $this->isValidType($elementArray[1]);
						if($result['isValid']) {
							$structure->{trim($elementArray[0])} = $result['type'];
						} else {
							throw new \RuntimeException('Invalid Data Type:'.$elementArray[1]);
						}
					}
				}
			}
		}
		return $structure;
	}

	/**
	 * Get TypeStruct class instance
	 * @param  array|object $data
	 * @return instance
	 */
	public function getTypeStruct($data)
	{
		if(!is_array($data) && !is_object($data)) 
			throw new \RuntimeException('TypeStruct Error: Parameter must be of type object or array');
		$this->info['data'] = (object)$data;
		require_once $this->gInfo['php'];
		$class 		= "\\".$this->info['full_name'];
		$struct 	= new $class($this->info['data'], $this->structure, $this->validateFull);
		return $struct;
	}

	/**
	 * Find Array Type of key
	 * @param  string $type
	 * @return string
	 */
	private function findArrayType(string $type): string
	{
		$infoArr = array_filter(explode('[]', $type));
		return (sizeof($infoArr)> 0 && trim($infoArr[0]))? $infoArr[0]: '[]';
	}

	/**
	 * Check if type is array
	 * @param  string  $type
	 * @return boolean
	 */
	private function isTypeArray(string $type): bool
	{
		return (preg_match("/(array|\[\])/i", $type));
	}

	/**
	 * Check if type is valid
	 * @param  string  $type
	 * @return array
	 */
	private function isValidType(string $type): array
	{
		$type 	= trim($type);
		$result = ['isValid' => false, 'type' => ''];
		if($this->isTypeArray($type)) {
			$arrayType = trim($this->findArrayType($type));
			if(in_array($arrayType, $this->arrayTypes)) {
				$result['isValid'] 	= true;
				$result['type'] 	= ['type' => 'array'];
				if($arrayType != '[]' && $arrayType != 'array') {
					$result['type']['of'] = $arrayType;
				}
			} else {
				$info = $this->checkResourceType(str_replace('[]', '', $type));
				if($info['isValid']) {
					$result['isValid'] 	= true;
					$result['type'] 	= ['type' => 'array', 'of' => $info['type']];
				}
			}
		} else {
			if(in_array($type, $this->reservedTypes)) {
				$result['isValid'] 	= true;
				$result['type'] 	= $type;
			} else {
				$info = $this->checkResourceType($type);
				if($info['isValid']) {
					$result['isValid'] 	= true;
					$result['type'] 	= $info['type'];
				}
			}
		}
		return $result;
	}

	/**
	 * Check Resource Type full class name
	 * @param  string $type
	 * @return array
	 */
	private function checkResourceType(string $type): array
	{
		$resource 	= $type;
		$found 		= false;
		$info 		= ['isValid' => true, 'type' => $type];
		if(sizeof($this->info['used_classes'])> 0) {
			foreach($this->info['used_classes'] as $class) {
				if(strpos($class, $type) !== false) {
					$found 		= true;
					$resource 	= $class; break;
				}
			}
		}
		$info['type'] = $resource;
		return $info;
	}

	/**
	 * Extract Dictionary from typestruct file
	 * @param  string $string
	 * @return array
	 */
	public function extractDictionary(string $string): array
	{
		preg_match_all('/{((?:[^{}]*|(?R))*)}/x', $string, $matches);
		return isset($matches[1])? $matches[1]: [];
	}
}