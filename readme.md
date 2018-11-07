[![Stable Version]()](https://packagist.org/packages/amsify42/typestruct) 
[![Downloads]()](https://packagist.org/packages/amsify42/typestruct) 
[![License](https://github.com/amsify42/typestruct/blob/master/LICENSE.txt)](https://packagist.org/packages/amsify42/typestruct)

## TypeStruct

PHP library for defining strictly typed multi-level object structure.

### Installation

```txt
composer require amsify42/typestruct
```

## Table of Contents
1. [Registering Autoloader](#registering-autoloader)
2. [Typestruct file](#typestruct-file)
3. [Usage](#usage)
4. [Multi Level Example](#multi-level-example)
5. [Data Types](#data-types)
6. [Built-in Functions](#built-in-functions)
7. [Manual Validation](#manual-validation)

### Registering Autoloader

```php
require_once __DIR__ . '/../vendor/autoload.php';

// Create Instance of Autoloader
$autoLoader = new Amsify42\TypeStruct\AutoLoader();

// If set to true will return all the error messages in response else it will throw exception on validation error
$autoLoader->setValidateFull(true);

// Pass the base namespace of your typestruct files
$autoLoader->setBaseNamespace(\App\TypeStructs::class);

// If your class files are not residing in psr4 directory structure, you can set callback for converting class name to locate the exact path of typestruct file while autoloading
$autoLoader->setCustom(function($class){
	// Do something with $class variable to convert it to the real path and return it.
	return 'your/path/to/class.php';
});

$autoLoader->register();
```

### Typestruct file

After registering is done, you can create your typestruct file
```php
namespace App\TypeStructs;

export typestruct Simple {
	id: int,
	name: string,
	price: float
}
```
**Note:** This is not php syntax, this file content will be converted to php equivalent class while processing.

### Usage

```php
$object = new \stdClass();
$object->id = 42;
$object->name = 'Prod42';
$object->price = 4.2;
$struct = new \App\TypeStructs\Simple($object);
$response = $struct->getResponse();
```
You will get type errors incase your object structure and its types does not match with **Simple** typestruct structure<br/><br/>
If you set **setValidateFull()** while registering as true, you will receive errors in **getResponse()** else exeception will be thrown while validating itself

<br/><br/>
Suppose your object is validated as true, now you want to change the property of the typestruct object

```php
$struct->id = '23'; // You'll receive an exception error as id is of type int and you tried to assign string
```
Even though the properties of struct object is of types objects, you can print it as usual
```php
echo $struct->id;	
```
But if you are looking to perform operations on these properties, you need to call its value method
```php
echo $struct->price->value()/2;	
```

### Multi Level Example
You can create typestruct of multi-level object which resembles the javascript object or json structure

```php
namespace Amsify42\TypeStructs;

use App\User;

export typestruct Sample {
	name: string,
	email: string,
	id: int,
	address: {
		door: string,
		zip: int
	},
	items: [],
	user : User,
	someEl: {
		key1: string,
		key2: int,
		key12: array,
		records: App\Record[],
		someChild: {
			key3: boolean,
			key4: float,
			someAgainChild: {
				key5: string,
				key6: float,
				key56: boolean[]
			}
		}
	}
}
```
All the above key value pairs will be validated based on their types.

### Data Types

#### Supported Data Types
1. string
2. int
3. float
4. boolean
5. null
6. any
7. YourClass

**Note:** 7th type is a class type, it can be any class.

#### For Array types
1. array
2. []
3. string[]
4. int[]
5. float[]
6. boolean[]
7. YourClass[]

**Note:** Both **array** and **[]** are same and represent the general or mixed array, 7th array is of type class resource.

#### Usage
You can instantiate data types from data type classes, below are the examples:
```php
	$string = new Amsify42\Typestruct\DataType\TypeString('some_string');
	$int 	= new Amsify42\Typestruct\DataType\TypeInt(42);
	$float 	= new Amsify42\Typestruct\DataType\TypeFloat(4.2);
	// For Array the default value of 2nd param is 'mixed', you can pass other data type listed above
	$array 	= new Amsify42\Typestruct\DataType\TypeArray([4, 2], 'mixed');
	$bool 	= new Amsify42\Typestruct\DataType\TypeBool(true);
```
For dynamically getting instance of value you don't know which type it is
```php
	$value = Amsify42\TypeStruct\Helper\DataType::getValue($variable);
```

### Built-in Functions
You can call built-in global functions of PHP as a chain with the properties of typestruct object.
```php
$string->explode(',')->implode(',');
```
**Note:** You can call only those built-in functions which either takes only one param or which takes last param as value of variable.
<br/><br/>
Examples:
```php
$value = 'typestruct';
addslashes($value); // It takes only one param, that is the value
explode(',', $value); // Even though it takes multiple params, it takes last param as value of variable
```

### Manual Validation
You can validate the data with typestruct structure without using autoloader

#### TypeStruct File
```php
namespace App\TypeStructs;

export typestruct Address {
	door: string,
	pincode: int,
	city: string
}	
```
#### Validation
```php
$validator = new Amsify42\TypeStruct\TypeStruct();

// Use this if your typestruct file resides in psr4 directory structure
$validator->setClass(\App\TypeStructs\Address::class);
// [OR] use this to locate exact path
$validator->setPath('path/to/Address.php');
```
You can pass both array or object of same structure which **Address** typestruct expects
```php	
$address ['door' => '10-11-1323', 'pincode' => 524278, 'city' => 'MyCity'];
// [OR]
$address = new \stdClass;
$address->door = '10-11-1323';
$address->pincode = 524278;
$address->city = 'MyCity';
```
You can get the struct object here in both case, whether validated or not.
```php
$struct = $validator->getTypeStruct($address);
```