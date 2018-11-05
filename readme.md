
## TypeStruct

PHP library for defining strictly typed multi-level object structure.

### Installation

```txt
composer require "amsify42/typestruct":"dev-master"
```

### Registering Autoloader for TypeStruct

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

### Create typestruct file

After registering is done, you can create your typestruct file
```php
namespace App\TypeStructs;

export typestruct Simple {
	id: int,
	name: string
}
```

### Usage

```php
$object = new \stdClass();
$object->id = 42;
$object->name = 'Prod42';
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

### Supported Data Types
1. string
2. int
3. float
4. boolean
5. null
6. any
7. YourClass

### For Array types
1. array
2. []
3. string[]
4. int[]
5. float[]
6. boolean[]
7. YourClass[]

Both **array** and **[]** are same and represent the general or mixed array, 7th array is of type class resource.