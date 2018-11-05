<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Core\DataType;

final class TypeString extends DataType
{
	/**
     * Instantiate string
     * @param string  $value
     */
	function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * For printing value when used in echo
	 * @return string
	 */
	public function __toString()
	{
		return $this->value;
	}
}