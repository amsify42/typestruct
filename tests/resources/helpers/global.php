<?php

function dumP()
{
	$args = func_get_args();
	if(sizeof($args)) {
		foreach($args as $akey => $arg) {
			if(PHP_SAPI === 'cli') {
				var_dump($arg);
			} else {
				highlight_string("<?php\n " . var_export($arg, true) . "?>");
				echo '<br/><hr>';
			}
		}
	}
}

function tresource($path)
{
	return __DIR__.'/../../resources/'.$path;
}


function executionTime($start)
{
	$end = microtime(true);
	$execute = ($end - $start);
	echo '<br/>Execution Time: '.$execute.' <br/>';
}