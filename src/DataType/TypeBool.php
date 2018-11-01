<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Core\DataType;

final class TypeBool extends DataType
{
	protected $value;

	function __construct(bool $value)
	{
		$this->value = $value;
	}

	public function __toString()
	{
		return $this->value;
	}
}