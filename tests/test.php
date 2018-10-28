<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Using Whoops for testing
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$execTime 	= true;
$test 		= 'core'; // ('core', 'class')


if($execTime) $start = microtime(true);

if($test == 'core') {
	$typeValidator = new Amsify42\TypeStruct\TypeStruct();
	// Set Class
	$typeValidator->setClass(\TestTS\resources\structs\Struct::class);
	// Set actual path of struct
	//$typeValidator->setPath(tresource('Struct.php'));
	//$typeValidator->setToken('.');
	$typeValidator->setValidateFull(true);
	//dumP($typeValidator->getStructure());
	//dumP($typeValidator->toJson());
	$passedData 		= json_decode(file_get_contents(tresource('struct.json')));
	$passedData->user 	= new \TestTS\resources\app\User;
	//$passedData->someEl->record = new App\Record;
	$passedData->someEl->records = [new \TestTS\resources\app\Record, new \TestTS\resources\app\Record];
	//dumP($passedData);
	$typeValidator->validate($passedData);
	$struct = $typeValidator->getTypeStruct();
	//dumP($struct); die;
	
	echo $struct->name.'<br/>';
	$struct->id = 3;
	echo ($struct->id->value()/2).'<br/>';
	echo $struct->someEl->someChild->key4.'<br/>';
	echo count($struct->items).'<br/>';
	echo $struct->items[1].'<br/>';
	foreach($struct->items as $key => $item) {
		echo $item.'<br/>';	
	}

	dumP($struct->someEl->someChild->someAgainChild);
	$struct->someEl->someChild->someAgainChild->key56 = [true, false, false];
	dumP($struct->someEl->someChild->someAgainChild->key56);
	dumP($struct->someEl->someChild->someAgainChild);
	$struct->someEl->someChild->someAgainChild->key5 = 'new string';
	dumP($struct->someEl->someChild->someAgainChild->key5);

} else if($test == 'class') {

	$object 		= new \stdClass();
	$object->id 	= 42;
	$object->name 	= 'Prod42';
	$object->price 	= 42.42;
	$object->mixed 	= '4354';
	$myclass 		= new \TestTS\MyTiClass();
	$myclass->test($object, 2);

	$simple 		= $myclass->getSimple();
	$simple->id = 2;
	dumP($simple->id);
	echo $simple->id;
}

if($execTime) executionTime($start);