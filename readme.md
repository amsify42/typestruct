
## TypeStruct

PHP library for defining strictly typed multi-level object structure.

### Installation

```txt
composer require "amsify42/typestruct":"dev-master"
```

## Table of Contents
1. [Registering Autoloader](#registering-autoloader)
2. [Typestruct file](#typestruct-file)
3. [Usage](#usage)
4. [Multi Level Example](#multi-level-example)
5. [Data Types](#data-types)
6. [Built-in Functions](#built-in-functions)

### Registering Autoloader

```php
require_once __DIR__ . '/../vendor/autoload.php';

// Create Instance of Autoloader
$autoLoader = new Amsify42\TypeStruct\AutoLoader();

// If set to true will return all the error messages in response else it will throw exception on validation error
$autoLoader->setValidateFull(true);

// Pass the base namespace of your typestruct files
$autoLoader->setBaseNamespace(\TestTS\resources\structs::class);

// If your class files are not residing in psr4 directory structure, you can set callback for converting class name to locate the exact path of typestruct file while autoloading
$autoLoader->setCustom(function($class){
	// Do something with $class variable to convert it to the real path and return it.
	return 'your/path/to/class.php';
});

// Done
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


### Built-in Functions
You can call built-in global functions of PHP as a chain with the properties of typestruct object.
```php
$struct->name->explode(',')->implode(',');
```
**Note:** You can call only those built-in functions which either takes only one param or which takes last param as value of variable.
<br/>
Examples:
```php
	$value = 'typestruct';
	addslashes($value); // It takes only one param, that is the value
	explode(',', $value); // Even though it takes multiple params, it takes last param as value of variable
```