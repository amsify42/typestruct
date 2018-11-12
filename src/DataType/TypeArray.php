<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Helper\DataType;

final class TypeArray implements \Iterator, \ArrayAccess, \Countable 
{
    /**
     * Position of array
     * @var integer
     */
	private $position = 0;

    /**
     * Array data
     * @var array
     */
	private $array;

    /**
     * Type of Array 
     * @var string
     */
    private $type;

    /**
     * Instantiate and validate array
     * @param array  $array
     * @param string $type
     */
	function __construct(array $array, string $type = 'mixed')
	{
        $this->array    = $this->validate($array, $type);
        $this->type     = $type;
        $this->position = 0;
	}

    /**
     * Call pre defined functions if exist
     * @param  string   $name
     * @param  array    $arguments
     * @return mixed
     */
    function __call($name, $arguments)
    {
        $name = decideFunction($name, 'array_');
        if($name) {
            if(count($arguments)> 0 && in_array($name, TS_G_FUNCTIONS)) {
                array_unshift($arguments, $this->array);
            } else {
                $arguments[] = $this->array;
            }
            return DataType::getValue($name(...$arguments));
        }
    }

    private function validate(array $array, string $type): array
    {
        $newArray = [];
        if($type == 'mixed') {
            foreach($array as $nak => $value) {
                $newArray[$nak] = DataType::getValue($value);
            }
        } else {
            foreach($array as $nak => $value) {
                if(DataType::isValid($value, $type)) {
                    $newArray[$nak] = DataType::getValue($value);
                } else {
                    throw new \RuntimeException("Array of type must be:".$type);
                }
            }
        }
        return $newArray;
    }

    /**
     * Get Type of array
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Iterator Methods
     */
    
	public function rewind()
	{
        $this->position = 0;
    }

    public function current()
    {
        return $this->array[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->array[$this->position]);
    }

    /**
     * ArrayAccess Methods
     */

    public function offsetSet($offset, $value)
    {
        if(DataType::isValid($value, $this->type)) {
            if(is_null($offset)) {
                $this->array[] = DataType::getValue($value);
            } else {
                $this->array[$offset] = DataType::getValue($value);
            }
        } else {
            throw new \RuntimeException("Array Type Error: Trying to assign '".DataType::getType($value)."' expected '".$this->type."'");
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    /**
     * Countable Method
     */
    
    public function count()
    {
        return count($this->array);
    }
}