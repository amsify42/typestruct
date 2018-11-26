<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
// Using Whoops for testing
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$execTime 	= true;

if($execTime) $start = microtime(true);

$autoLoader = new Amsify42\TypeStruct\AutoLoader();
$autoLoader->setBaseNamespace(\TestTS\resources\structs::class);
$autoLoader->register();

$object 				= new \stdClass();
$object->id 			= 42443;
$object->name 			= 'Prod42';
$object->price 			= 42.42;
$object->accessories 	= ['one', 'two', 'three', 'four'];
$struct 				= new \TestTS\resources\structs\Size($object);

$struct->price 			= 43466.01;
$struct->accessories 	= ['one', 'two', 'three', 'four', 'five'];
dumP($struct->getData());

if($execTime) executionTime($start);