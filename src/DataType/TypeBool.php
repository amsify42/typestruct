<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Core\DataType;

final class TypeBool extends DataType
{
	/**
     * Instantiate bool
     * @param bool  $value
     */
	function __construct(bool $value)
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