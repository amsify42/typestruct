<?php

if(!function_exists('resource'))
{
	function resource($path)
	{
		return __DIR__.'/../resources/'.$path;
	}
}

if(!function_exists('tsencode'))
{
	function tsencode($string)
	{
		return strtolower(urlencode(base64_encode($string)));
	}
}

if(!function_exists('tsdecode'))
{
	function tsdecode($string)
	{
		return base64_decode(urldecode($string));
	}
}