<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\Core\Resource;
use stdClass;

class TypeStruct extends Resource
{
	private $reservedTypes 	= ['string', 'int', 'float', 'boolean', 'null', 'any']; 
	private $arrayTypes 	= ['array', '[]', 'string', 'int', 'float', 'boolean'];
	private $originalContent;
	private $content;
	private $token 			= '->';
	private $validateFull 	= false;

	protected $structure;

	public 	$info 			= [];

	public function __construct()
	{
		define('TS_SRC_PATH', __DIR__);
	}

	public function setToken($token = '->')
	{
		$this->token = $token;
	}

	public function setClass($class)
	{
		$path = $this->getAutoloadPsr4Path($class);
		if($path) {
			$this->info['path'] = $path;
			$this->extractStructure();
		} else {
			throw new \RuntimeException("Class: '".$class."' not found");
		}
	}

	public function setPath($path)
	{
		$this->info['path'] = $path;
		$this->extractStructure();
	}

	public function setValidateFull($isFull)
	{
		$this->validateFull = $isFull;
	}

	private function extractStructure()
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

	private function findClassName()
	{
		$fullName = '';
		preg_match_all('/typestruct(.*?){/ims', $this->content, $matches);
		if(isset($matches[1])) {
			$this->info['name'] = trim($matches[1][0]);
		}
	}

	private function findFullClassName()
	{
		$fullName = '';
		preg_match_all('/namespace(.*?);/ims', $this->content, $matches);
		if(isset($matches[1])) {
			$this->info['namespace'] = trim($matches[1][0]);
			$fullName = $this->info['namespace']."\\".trim($this->info['name']);
		} else {
			$fullName = $this->info['name'];
		}
		$this->info['full_name'] = $fullName;
	}

	private function findUsedNamespaces()
	{
		$classes = [];
		preg_match_all('/use(.*?);/ims', $this->content, $matches);
		if(isset($matches[1])) {
			$classes = array_map('trim', $matches[1]);
		}
		$this->info['used_classes'] = $classes;
	}

	public function getStructure()
	{
		return $this->structure;
	}

	public function toJson()
	{
		return json_encode($this->structure);
	}

	public function structToObject($structString)
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

	public function getTypeStruct($data)
	{
		if(!is_array($data) && !is_object($data)) 
			throw new \RuntimeException('TypeStruct Error: Parameter must be of type object or array');
		$this->info['data'] = (object)$data;
		require_once $this->gInfo['php'];
		$class 		= "\\".$this->info['full_name'];
		$struct 	= new $class($this->info['data'], $this->structure);
		return $struct;
	}

	private function findArrayType($type)
	{
		$infoArr = array_filter(explode('[]', $type));
		return (sizeof($infoArr)> 0 && trim($infoArr[0]))? $infoArr[0]: '[]';
	}

	private function isTypeArray($type)
	{
		return (preg_match("/(array|\[\])/i", $type));
	}

	private function isValidType($type)
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

	private function checkResourceType($type)
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

	public function extractDictionary($string)
	{
		preg_match_all('/{((?:[^{}]*|(?R))*)}/x', $string, $matches);
		return isset($matches[1])? $matches[1]: [];
	}
}