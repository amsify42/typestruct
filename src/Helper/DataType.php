<?php

namespace Amsify42\TypeStruct\Helper;

use Amsify42\TypeStruct\DataType as DataTypes;
use Amsify42\TypeStruct\Core\DataType as DtType;
use stdClass;

class DataType
{
	/**
	 * Check if the type of value is valid
	 * @param  mixed  $value
	 * @param  string  $type
	 * @return boolean
	 */
	public static function isValid($value, string $type): bool
	{
		$valid = false;
		if($type == 'mixed') {
			$valid = true;
		} else if(is_string($value) || $value instanceof DataTypes\TypeString) {
			if($type == 'string') $valid = true;
		} else if(is_int($value) || $value instanceof DataTypes\TypeInt) {
			if($type == 'int') $valid = true;
		} else if(is_float($value) || $value instanceof DataTypes\TypeFloat) {
			if($type == 'float') $valid = true;
		} else if(is_array($value) || $value instanceof DataTypes\TypeArray) {
			if($type == 'array') $valid = true;
		} else if(is_bool($value) || $value instanceof DataTypes\TypeBool) {
			if($type == 'boolean') $valid = true;
		} else if(is_object($value)) {
			if($value instanceof $type) $valid = true;
		}
		return $valid;	
	}

	/**
	 * Get type of value given
	 * @param  mixed $value
	 * @return string
	 */
	public static function getType($value): string
	{
		if(is_string($value)) {
			return 'string';
		} else if(is_int($value)) {
			return 'integer';
		} else if(is_float($value)) {
			return 'float';
		} else if(is_array($value)) {
			return 'array';
		} else if(is_bool($value)) {
			return 'boolean';
		} else if(is_object($value)) {
			return 'object';
		}
		return 'different';
	}

	/**
	 * Get instance of value type
	 * @param  mixed $value
	 * @return mixed
	 */
	public static function getValue($value)
	{
		if(is_string($value)) {
			if($value instanceof DataTypes\TypeString) {
				return $value;
			} else {
				return new DataTypes\TypeString($value);
			}
		} else if(is_int($value)) {
			if($value instanceof DataTypes\TypeInt) {
				return $value;
			} else {
				return new DataTypes\TypeInt($value);
			}
		} else if(is_float($value)) {
			if($value instanceof DataTypes\TypeFloat) {
				return $value;
			} else {
				return new DataTypes\TypeFloat($value);
			}
		} else if(is_array($value)) {
			if($value instanceof DataTypes\TypeArray) {
				return $value;
			} else {
				return new DataTypes\TypeArray($value);
			}
		} else if(is_bool($value)) {
			if($value instanceof DataTypes\TypeBool) {
				return $value;
			} else {
				return new DataTypes\TypeBool($value);
			}
		}
		return $value;
	}

	/**
	 * Get Instance of value
	 * @param  mixed 		$value
	 * @param  string|array $type
	 * @return mixed
	 */
	public static function getInstance($value, $type)
	{
		if($type['type'] == 'array') {
			if($value instanceof DataTypes\TypeArray) {
				return $value;
			} else {
				return new DataTypes\TypeArray($value, isset($type['of'])? $type['of']: 'mixed');
			}
		} else if($type['type'] == 'string') {
			if($value instanceof DataTypes\TypeString) {
				return $value;
			} else {
				return new DataTypes\TypeString($value, isset($type['length'])? $type['length']: 0);
			}
		} else if($type['type'] == 'int') {
			if($value instanceof DataTypes\TypeInt) {
				return $value;
			} else {
				return new DataTypes\TypeInt($value, isset($type['length'])? $type['length']: 0);
			}
		} else if($type['type'] == 'float') {
			if($value instanceof DataTypes\TypeFloat) {
				return $value;
			} else {
				return new DataTypes\TypeFloat($value, isset($type['length'])? $type['length']: 0, isset($type['decimal'])? $type['decimal']: 0);
			}
		} else if($type['type'] == 'bool') {
			if($value instanceof DataTypes\TypeBool) {
				return $value;
			} else {
				return new DataTypes\TypeBool($value);
			}
		} else {
			return self::getValue($value);
		}
	}

	/**
	 * assign value to property of typestruct object
	 * @param  string 	$name
	 * @param  mixed  	$property
	 * @param  mixed 	$value
	 * @return mixed
	 */
	public static function assign(string $name, $property, $value)
	{
		$type 		= 'mixed';
		$isAssign 	= true;
		if($property instanceof DataTypes\TypeString) {
			if(is_string($value)) {
				return new DataTypes\TypeString($value, $property->getLength());
			} else if($value instanceof DataTypes\TypeString && $value->getLength() == $property->getLength()) {
				return $value;
			} else {
				$type 		= 'string';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeInt) {
			if(is_int($value)) {
				return new DataTypes\TypeInt($value, $property->getLength());
			} else if($value instanceof DataTypes\TypeInt && $value->getLength() == $property->getLength()) {
				return $value;
			} else {
				$type 		= 'integer';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeFloat) {
			if(is_float($value)) {
				return new DataTypes\TypeFloat($value, $property->getLength(), $property->getDecimal());
			} else if($value instanceof DataTypes\TypeFloat && $value->getLength() == $property->getLength() && $value->getDecimal() == $property->getDecimal()) {
				return $value;
			} else {
				$type 		= 'float';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeBool) {
			if(is_bool($value)) {
				return new DataTypes\TypeBool($value);
			} else if($value instanceof DataTypes\TypeBool) {
				return $value;
			} else {
				$type 		= 'float';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeArray) {
			if(is_array($value)) {
				return new DataTypes\TypeArray($value, $property->getType());
			} else if($value instanceof DataTypes\TypeArray && $value->getLength() == $property->getLength()) {
				return $value;
			} else {
				$type 		= 'array';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\Struct) {
			if(is_object($value)) {
				return new DataTypes\Struct($value, $property->getStructure());
			} else {
				$type 		= 'Struct';
				$isAssign 	= false;
			}
		}

		if($isAssign) {
			return $value;
		} else {
			throw new \RuntimeException("Property: ".$name." - Trying to assign '".self::getType($value)."' expected '".$type."'");
		}
	}

	/**
	 * Converting struct object child objects to type struct
	 * @param  stdClass $object
	 * @param  stdClass $structure
	 * @param  boolean 	$isChild
	 * @param  boolean 	$isValidateFull
	 * @return mixed
	 */
	public static function childToStruct(stdClass $object, stdClass $structure, bool $isChild = false, bool $isValidateFull = false)
	{
		$stdObject 	= new stdClass;
		foreach($object as $name => $element)
		{
			if($element instanceof stdClass) {
				$stdObject->{$name} = self::childToStruct($element, $structure->{$name}, true, $isValidateFull);
			} else {
				$stdObject->{$name} = $element;
			}
		}
		return ($isChild)? new DataTypes\Struct($stdObject, $structure, $isValidateFull) :$stdObject; 
	}

	/**
	 * Check Type of value
	 * @param  string 	$name
	 * @param  mixed 	$value
	 * @param  array 	$type
	 * @return array
	 */
	public static function checkType(string $name, $value, array $type): array
	{
		$result = ['isValid' => true, 'message' => ''];
		$vType 	= $type['type'];
		switch($vType) {
			case 'string':
				if(!is_string($value) && !$value instanceof DataTypes\TypeString) {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' must be a string';
				}
				break;

			case 'int':
				if(!is_int($value) && !$value instanceof DataTypes\TypeInt) {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' must be a int';
				}
				break;

			case 'float':
				if(!is_float($value) && !$value instanceof DataTypes\TypeFloat) {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' must be a float';
				}
				break;
			case 'boolean':
				if(!is_bool($value) && !$value instanceof DataTypes\TypeBool) {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' must be a boolean';
				}
				break;

			case 'null':
				if($value !== NULL) {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' must be null';
				}
				break;

			case 'any':
				break;	

			default:
				if(strpos($vType, '\\') !== false || preg_match("/^[A-Z]/", $vType)) {
					if(!self::isResource($value, $vType)) {
						$result['isValid'] 	= false;
						$result['message'] 	= $name.' must be of type class: '.$vType;
					}
				} else {
					throw new \RuntimeException("Invalid data type: ".$vType);
				}
				break;
		}
		if($result['isValid'] && $type['length']) {
			$result = self::checkLength($value, $vType, $type['length']);
		}
		return $result;
	}

	public static function checkLength($value, string $type, int $length): array
	{
		$result = ['isValid' => true, 'message' => ''];
		$val 	= ($value instanceof DtType)? (string)$value->value(): (string)$value;
		if($type == 'float' || $value instanceof TypeFloat) {
			$val = explode('.', $val)[0];
		}
		$length = strlen($val);
		if($length > $length) {
			$result['isValid'] = false;
			$result['message'] = "Max length allowed for '".$name."' is ".$length;
		}
		return $result;
	}

	/**
	 * Check if value is of type given resource
	 * @param  mixed  $value
	 * @param  mixed  $type
	 * @return boolean
	 */
	public static function isResource($value, $type): bool
	{
		return ($value instanceof $type);
	}

	/**
	 * Check Array Type
	 * @param  string 	$name
	 * @param  mixed 	$value
	 * @param  array 	$info
	 * @return array
	 */
	public static function checkArrayType(string $name, $value, array $info): array
	{
		$result 		= ['isValid' => true, 'message' => '', 'value' => []];
		$isArray 		= is_array($value);
		$isTypeArray	= $value instanceof DataTypes\TypeArray;
		if($info['length']) {
			if(($isArray && sizeof($value) > $info['length']) || ($isTypeArray && $value->count() > $info['length'])) {
				$result['isValid'] = false;
				$result['message'] = "Max length allowed for '".$name."' is ".$info['length'];
				return $result;
			}
		}
		if(isset($info['of']) && ($isArray || $isTypeArray)) {
			if($info['of'] == 'string') {
				foreach($value as $vk => $el) {
					if(!is_string($el) && !$el instanceof DataTypes\TypeString) {
						$result['isValid'] 	= false;
						$result['message'] 	= $name.' must be an array of string';
						break;
					} else {
						$result['value'][$vk] = self::getInstance($el, ['type' => 'string']);
					}
				}
			} else if($info['of'] == 'int') {
				foreach($value as $vk => $el) {
					if(!is_int($el) && !$el instanceof DataTypes\TypeInt) {
						$result['isValid'] 	= false;
						$result['message'] 	= $name.' must be an array of int';
						break;
					} else {
						$result['value'][$vk] = self::getInstance($el, ['type' => 'int']);
					}
				}
			} else if($info['of'] == 'float') {
				foreach($value as $vk => $el) {
					if(!is_float($el) && !$el instanceof DataTypes\TypeFloat) {
						$result['isValid'] 	= false;
						$result['message'] 	= $name.' must be an array of float';
						break;
					} else {
						$result['value'][$vk] = self::getInstance($el, ['type' => 'float']);
					}
				}
			} else if($info['of'] == 'boolean') {
				foreach($value as $vk => $el) {
					if(!is_bool($el) && !$el instanceof DataTypes\TypeBool) {
						$result['isValid'] 	= false;
						$result['message'] 	= $name.' must be an array of boolean';
						break;
					}
				}
			} else {
				if(is_array($value) || $value instanceof DataTypes\TypeArray) {
					foreach($value as $vk => $el) {
						if(!self::isResource($el, $info['of'])) {
							$result['isValid'] 	= false;
							$result['message'] 	= $name.' must be array of type '.$info['of'];
							break;
						}
					}	
				} else {
					$result['isValid'] 	= false;
					$result['message'] 	= $name.' must be array of type '.$info['of'];
				}
			}
		} else {
			if(!$isArray && !$isTypeArray) {
				$result['isValid'] 	= false;
				$result['message'] 	= $name.' must be array';
			} else {
				foreach($value as $vk => $el) {
					$result['value'][$vk] = self::getValue($el);
				}
			}
		}
		return $result;
	}
}