<?php

namespace Amsify42\TypeStruct\Helper;

use Amsify42\TypeStruct\DataType as DataTypes;

class DataType
{
	public static function isValid($value, $type)
	{
		if($type == 'mixed') {
			return true;
		} else if(is_string($value) && $type == 'string') {
			return true;
		} else if(is_int($value) && $type == 'int') {
			return true;
		} else if(is_float($value) && $type == 'float') {
			return true;
		} else if(is_array($value) && $type == 'array') {
			return true;
		}
		return false;	
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
				return $property->assign($value);
			} else {
				$type 		= 'string';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeInt) {
			if(is_int($value)) {
				return $property->assign($value);
			} else {
				$type 		= 'integer';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeFloat) {
			if(is_float($value)) {
				return $property->assign($value);
			} else {
				$type 		= 'float';
				$isAssign 	= false;
			}
		} else if($property instanceof DataTypes\TypeArray) {
			if(is_array($value)) {
				return $property->assign($value);
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
}