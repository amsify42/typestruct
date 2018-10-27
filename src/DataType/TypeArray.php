<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Helper\DataType;

final class TypeArray implements \Iterator, \ArrayAccess, \Countable 
{
	private $position = 0;
	private $array;
    private $type;

	function __construct(array $array, string $type = 'mixed')
	{
		$this->array    = $array;
        $this->type     = $type;
        $this->position = 0;
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
        if(is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
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

    public function assign(array $array)
    {
        if($this->type == 'mixed') {
            $newArray = [];
            foreach($array as $nak => $value) {
                $newArray[$nak] = DataType::getValue($value);
            }
            return new self($newArray);
        } else {
            $newArray = [];
            foreach($array as $nak => $value) {
                if(DataType::isValid($value, $this->type)) {
                    $newArray[$nak] = DataType::getValue($value);
                } else {
                    throw new \RuntimeException("Array of type must be:".$this->type);
                }
            }
            return new self($newArray, $this->type);
        }
    }
}