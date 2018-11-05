<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Core\DataType;

final class TypeInt extends DataType
{
	/**
     * Instantiate int
     * @param int  $value
     */
	function __construct(int $value)
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