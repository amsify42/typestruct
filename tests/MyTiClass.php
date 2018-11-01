<?php

namespace TestTS;

use \TestTS\resources\structs\Simple;

class MyTiClass
{
	public function test(Simple $simple, $id)
	{
		dumP($simple, $id);
	}

	public function getSimple(): Simple
	{
		$object 		= new \stdClass();
		$object->id 	= 42;
		$object->name 	= 'Prod42';
		$object->price 	= 42.42;
		$object->mixed 	= 'dfgfd';
		return new Simple($object);
	}
}

