<?php

namespace Amsify42\TypeStruct\DataType;

use Amsify42\TypeStruct\Core\DataType;

final class TypeInt extends DataType
{
	protected $value;

	function __construct(int $value)
	{
		$this->value = $value;
	}

	public function __toString()
	{
		return (string)$this->value;
	}
}