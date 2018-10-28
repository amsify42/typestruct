<?php

namespace Amsify42\TypeStruct\Helper;

use Amsify42\TypeStruct\DataType as DataTypes;
use Amsify42\TypeStruct\Core\Struct;
use stdClass;

class DataType
{
	public static function isValid($value, $type)
	{
		$valid = false;
		if($type == 'mixed') {
			$valid = true;
		} else if(is_string($value)) {
			if($type == 'string') $valid = true;
		} else if(is_int($value)) {
			if($type == 'int') $valid = true;
		} else if(is_float($value)) {
			if($type == 'float') $valid = true;
		} else if(is_array($value)) {
			if($type == 'array') $valid = true;
		} else if(is_bool($value)) {
			if($type == 'boolean') $valid = true;
		} else if(is_object($value)) {
			if($value instanceof $type) $valid = true;
		}
		return $valid;	
	}

	public static function getValue($value)
	{
		if(is_string($value)) {
			return new DataTypes\TypeString($value);
		} else if(is_int($value)) {
			return new DataTypes\TypeInt($value);
		} else if(is_float($value)) {
			return new DataTypes\TypeFloat($value);
		} else if(is_array($value)) {
			return new DataTypes\TypeArray($value);
		}
		return $value;
	}

	public static function getInstance($value, $type)
	{
		if(is_array($type)) {
			return new DataTypes\TypeArray($value, isset($type['of'])? $type['of']: 'mixed');
		} else if($type == 'string') {
			return new DataTypes\TypeString($value);
		} else if($type == 'int') {
			return new DataTypes\TypeInt($value);
		} else if($type == 'float') {
			return new DataTypes\TypeFloat($value);
		} else {
			return self::getValue($value);
		}
	}

	public static function assign($name, $property, $value)
	{
		$type 		= 'mixed';
		$isAssign 	= true;
		if($property instanceof DataTypes\TypeString) {
			if(is_string($value)) {
				return new DataTypes\TypeString($value);
			} else {
				$type 		= 'string';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeInt) {
			if(is_int($value)) {
				return new DataTypes\TypeInt($value);
			} else {
				$type 		= 'integer';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeFloat) {
			if(is_float($value)) {
				return new DataTypes\TypeFloat($value);
			} else {
				$type 		= 'float';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeArray) {
			if(is_array($value)) {
				return new DataTypes\TypeArray($value, $property->getType());
			} else {
				$type 		= 'array';
				$isAssign 	= false;
			}
		}

		if($isAssign) {
			return $value;
		} else {
			throw new \RuntimeException("Property: ".$name." -- Trying to assign '".gettype($value)."' expected '".$type."'");
		}
	}

	public static function childToStruct($object, $isChild = false)
	{
		$stdObject 	= new stdClass;
		foreach($object as $name => $element)
		{
			if($element instanceof stdClass) {
				$stdObject->{$name} = self::childToStruct($element, true);
			} else {
				$stdObject->{$name} = $element;
			}
		}
		return ($isChild)? new Struct($stdObject): $stdObject; 
	}
}