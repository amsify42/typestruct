<?php

if(!function_exists('resource'))
{
	/**
	 * Gets the resource directory path
	 * @param  string $path
	 * @return string
	 */
	function resource(string $path): string
	{
		return __DIR__.'/../resources/'.$path;
	}
}


if(!function_exists('pathKey'))
{
	/**
	 * Multi-level Path Key of object separated by token
	 * @param  string $path
	 * @param  string $name
	 * @param  string $token
	 * @return string
	 */
	function pathKey($path, string $name, string $token = '->'): string
	{
		return ($path)? ltrim($path, $token).$token.$name: $name;
	}
}

if(!function_exists('decideFunction'))
{
	/**
	 * Find function exist
	 * @param  string $name
	 * @param  string $prefix
	 * @return string
	 */
	function decideFunction(string $name, string $prefix = ''): string
	{
		if(function_exists($name)) {
			return $name;
		} else {
			$_name = nameToUnderscore($name);
			if(function_exists($_name)) {
				return $_name;
			} else if($prefix) {
				$_name = nameToUnderscore($prefix.$name);
				if(function_exists($_name)) {
					return $_name;
				}
			}
		}
		return NULL;
	}
}

if(!function_exists('nameToUnderscore'))
{
	/**
	 * CamelCase To Underscore
	 * @param  string $name
	 * @return string
	 */
	function nameToUnderscore(string $name): string
	{
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $name));
	}
}

if(!function_exists('arrayToObject'))
{
	/**
	 * array to object
	 * @param  array $array
	 * @param  array $struct
	 * @return stdClass
	 */
	function arrayToObject(array $array, stdClass $struct): stdClass
	{
	  $obj = new stdClass;
	  foreach($array as $k => $v) {
	     if(strlen($k)) {
	     	if(isset($struct->{$k})) {
		        if(is_array($v) && !is_array($struct->{$k})) {
		           $obj->{$k} = arrayToObject($v, $struct->{$k});
		        } else {
		           $obj->{$k} = $v;
		        }
	        }
	     }
	  }
	  return $obj;
	}
}

/**
 * Data Types Global Helpers
 */
if(!function_exists('typeStr'))
{
	function typeStr(string $value)
	{
		return new \Amsify42\TypeStruct\DataType\TypeString($value);
	}
}

if(!function_exists('typeInt'))
{
	function typeInt(int $value)
	{
		return new \Amsify42\TypeStruct\DataType\TypeInt($value);
	}
}

if(!function_exists('typeFloat'))
{
	function typeFloat(float $value)
	{
		return new \Amsify42\TypeStruct\DataType\TypeFloat($value);
	}
}

if(!function_exists('typeBool'))
{
	function typeBool(bool $value)
	{
		return new \Amsify42\TypeStruct\DataType\TypeBool($value);
	}
}

if(!function_exists('typeArr'))
{
	function typeArr(array $value, string $type = 'mixed')
	{
		return new \Amsify42\TypeStruct\DataType\TypeArray($value, $type);
	}
}

if(!function_exists('tsTypeVal'))
{
	function tsTypeVal($value)
	{
		return \Amsify42\TypeStruct\Helper\DataType::getValue($value);
	}
}

/**
 * Functions which takes value as the last param
 */
if(!defined('TS_G_FUNCTIONS')) {
	define('TS_G_FUNCTIONS', [
		// string
		'addcslashes',
		'chop',
		'chunk_split',
		'convert_cyr_string',
		'crypt',
		'count_chars',
		'convert_cyr_string',
		'number_format',
		'nl2br',
		'metaphone',
		'ltrim',
		'levenshtein',
		'htmlspecialchars',
		'htmlspecialchars_decode',
		'htmlentities',
		'html_entity_decode',
		'hebrevc',
		'str_pad',
		'str_getcsv',
		'sscanf',
		'similar_text',
		'sha1',
		'rtrim',
		'strcspn',
		'strchr',
		'str_word_count',
		'str_split',
		'str_repeat',
		'strspn',
		'strrpos',
		'strripos',
		'strrchr',
		'strpos',
		'strpbrk',
		'strncmp',
		'strncasecmp',
		'stristr',
		'stripos',
		'strip_tags',
		'strstr',
		'strtok',
		'strtr',
		'substr',
		'substr_compare',
		'substr_count',
		'substr_replace',
		'trim',
		'wordwrap',
		// number
		'base_convert',
		'log',
		'round',
		// array
		'array_change_key_case',
		'array_chunk',
		'array_column',
		'array_filter',
		'array_keys',
		'array_pad',
		'array_push',
		'array_rand',
		'array_reduce',
		'array_reverse',
		'array_slice',
		'array_splice',
		'array_udiff',
		'array_udiff_assoc',
		'array_udiff_uassoc',
		'array_uintersect',
		'array_uintersect_assoc',
		'array_uintersect_uassoc',
		'array_unshift',
		'array_walk',
		'array_walk_recursive',
		'arsort',
		'asort',
		'extract',
		'krsort',
		'rsort',
		'sizeof',
		'sort',
		'uasort',
		'uksort',
		'usort',
	]);
}