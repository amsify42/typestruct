<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TypeStructTest extends TestCase
{
	/**
     * @dataProvider arrayProvider
     */
	public function testResponse($data)
	{
		$struct = new \TestTS\resources\structs\Simple($data);
		$this->assertTrue($struct->getResponse()['isValid']);
	}

	public function arrayProvider()
	{
		return [
			[
				['id' => 1, 'name' => 'Sami', 'price' => 12.2, 'mixed' => 'some']
			],
            [
            	['id' => 2, 'name' => 'Fasi', 'price' => 17.4, 'mixed' => 'somet']
            ],
            [
            	['id' => 3, 'name' => 'Masi', 'price' => 18.5, 'mixed' => 'somep']
            ],
            [
            	['id' => 4, 'name' => 'Safi', 'price' => 19.6, 'mixed' => 'somew']
            ],
		];
	}
}


$autoLoader = new Amsify42\TypeStruct\AutoLoader();
$autoLoader->setBaseNamespace(\TestTS\resources\structs::class);
$autoLoader->setValidateFull(true);
$autoLoader->setCustom(function($class){
	$someInfo 	= explode('\\', $class);
	return __DIR__."/../resources/structs/".end($someInfo).".php";
});
$autoLoader->register();