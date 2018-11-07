<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Using Whoops for testing
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$execTime 	= true;
$test 		= 'autoload'; // ('core', 'class', 'simple', 'autoload')


if($execTime) $start = microtime(true);

if($test == 'core') {

	$typeValidator = new Amsify42\TypeStruct\TypeStruct();
	$typeValidator->setValidateFull(true);
	// Set Class
	$typeValidator->setClass(\TestTS\resources\structs\Struct::class);
	// Set actual path of struct
	//$typeValidator->setPath(tresource('Struct.php'));
	//$typeValidator->setToken('.');
	//dumP($typeValidator->getStructure());
	//dumP($typeValidator->toJson());
	$passedData 		= json_decode(file_get_contents(tresource('struct.json')));
	$passedData->user 	= new \TestTS\resources\app\User;
	//$passedData->someEl->record = new App\Record;
	$passedData->someEl->records = [new \TestTS\resources\app\Record, new \TestTS\resources\app\Record];
	//dumP($passedData);
	//$typeValidator->validate($passedData);
	$struct = $typeValidator->getTypeStruct($passedData);
	//$struct = 1;
	//dumP($struct); die;

	echo $struct->name.'<br/>';
	$array = $struct->name->explode(',');
	dumP($array);
	
	// echo $struct->name.'<br/>';
	// $struct->id = 3;
	// echo ($struct->id->value()/2).'<br/>';
	// echo $struct->someEl->someChild->key4.'<br/>';
	// echo count($struct->items).'<br/>';
	// echo $struct->items[1].'<br/><br/>';
	// foreach($struct->items as $key => $item) {
	// 	echo $item.'<br/>';	
	// }
	// $string = $struct->items->implode(',');
	// dumP($string);
	// die;

	// dumP($struct->someEl->someChild->someAgainChild);
	// $obj = new \stdClass();
	// $obj->key5 = 'string';
	// $obj->key6 = new Amsify42\TypeStruct\DataType\TypeFloat(2);
	// $obj->key56 = [false, false, true];
	// $struct->someEl->someChild->someAgainChild = $obj;
	// dumP($struct->someEl->someChild->someAgainChild);


	// $struct->someEl->someChild->someAgainChild->key56 = [true, false, false];
	// dumP($struct->someEl->someChild->someAgainChild->key56);
	// dumP($struct->someEl->someChild->someAgainChild);
	// $struct->someEl->someChild->someAgainChild->key5 = 'new string';
	// dumP($struct->someEl->someChild->someAgainChild->key5);

} else if($test == 'class') {

	$autoLoader = new Amsify42\TypeStruct\AutoLoader();
	$autoLoader->setBaseNamespace(\TestTS\resources\structs::class);
	$autoLoader->register();

	$object 		= new \stdClass();
	$object->id 	= 42;
	$object->name 	= 'Prod42';
	$object->price 	= new Amsify42\TypeStruct\DataType\TypeFloat(42);
	$object->mixed 	= '4354';
	$myclass 		= new \TestTS\MyTiClass();
	$myclass->test(new \TestTS\resources\structs\Simple($object), 2);

	$simple 		= $myclass->getSimple();
	$simple->id 	= 2;
	dumP($simple->id);
	echo $simple->id;

} else if($test == 'simple') {

	$autoLoader = new Amsify42\TypeStruct\AutoLoader();
	$autoLoader->setBaseNamespace(\TestTS\resources\structs::class);
	$autoLoader->register();

	$object 		= new \stdClass();
	$object->id 	= 42;
	$object->name 	= 'Prod42';
	$object->price 	= 42.42;
	$object->mixed 	= '4354';
	$struct 		= new \TestTS\resources\structs\Simple($object);
	dumP($struct);

} else if($test == 'autoload') {

	$autoLoader = new Amsify42\TypeStruct\AutoLoader();
	$autoLoader->setBaseNamespace(\TestTS\resources\structs::class);
	$autoLoader->setValidateFull(true);
	$autoLoader->setCustom(function($class){
		$someInfo 	= explode('\\', $class);
		return __DIR__."/resources/structs/".end($someInfo).".php";
	});
	$autoLoader->register();

	$object 		= new \stdClass();
	$object->id 	= 42;
	$object->name 	= 'Prod42';
	$object->price 	= 42;
	$object->mixed 	= '4354';
	$struct 		= new \TestTS\resources\structs\Simple($object);
	dumP($struct->getResponse());
	//dumP($struct);
	
}

if($execTime) executionTime($start);