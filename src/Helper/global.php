<?php

if(!function_exists('resource'))
{
	/**
	 * Gets the resource directory path
	 * @param  string $path
	 * @return string
	 */
	function resource($path)
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
	function pathKey($path, $name, $token = '->')
	{
		return ($path)? ltrim($path, $token).$token.$name: $name;
	}
}