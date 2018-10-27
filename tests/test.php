<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Using Whoops for testing
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$execTime 	= false;
$test 		= 'core'; // ('core', 'class')


if($execTime) $start = microtime(true);

if($test == 'core') {
	$typeValidator = new Amsify42\TypeStruct\TypeStruct();
	// Set Class
	$typeValidator->setClass(\TestTS\resources\interfaces\Struct::class);
	// Set actual path of interface
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
	$interface = $typeValidator->getInterface();
	//dumP($interface); die;
	echo $interface->name.'<br/>';
	$interface->id = 3;
	echo ($interface->id->value()/2).'<br/>';
	echo $interface->someEl->someChild->key4.'<br/>';
	echo count($interface->items).'<br/>';
	echo $interface->items[1].'<br/>';
	foreach($interface->items as $key => $item) {
		echo $item.'<br/>';	
	}

	dumP($interface->someEl->someChild->someAgainChild);
	$interface->someEl->someChild->someAgainChild->key56 = [1, 'one'];
	dumP($interface->someEl->someChild->someAgainChild->key56);

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