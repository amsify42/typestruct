<?php

namespace TestTS;

use \TestTS\resources\interfaces\Simple;

class MyTiClass extends \Amsify42\TypeStruct\Core\Validator
{
	protected $baseNameSpace 	= \TestTS\resources\interfaces::class;
	protected $validateFull 	= true;

	protected function test(Simple $simple, $id)
	{
		dumP($simple, $id);
	}

	protected function getSimple(): Simple
	{
		$object 		= new \stdClass();
		$object->id 	= 42;
		$object->name 	= 'Prod42';
		$object->price 	= 42.42;
		$object->mixed 	= 'dfgfd';
		return $this->validateReturn($object);
	}
}

