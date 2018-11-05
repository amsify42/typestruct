<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Core\DataType;

final class TypeFloat extends DataType
{
	/**
     * Instantiate float
     * @param float  $value
     */
	function __construct(float $value)
	{
		$this->value = $value;
	}

	/**
	 * For printing value when used in echo
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->value;
	}
}