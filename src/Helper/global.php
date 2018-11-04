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

if(!function_exists('tsencode'))
{
	/**
	 * encodes the string
	 * @param  string $string
	 * @return string
	 */
	function tsencode($string)
	{
		return strtolower(urlencode(base64_encode($string)));
	}
}

if(!function_exists('tsdecode'))
{
	/**
	 * decodes the string
	 * @param  string $string
	 * @return string
	 */
	function tsdecode($string)
	{
		return base64_decode(urldecode($string));
	}
}