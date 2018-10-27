<?php

namespace Amsify42\TypeStruct\Exception;

class Handler
{
	public $handler;

	function __construct()
	{
		$this->handler = new \Whoops\Run();
		$this->handler->pushHandler(new \Whoops\Handler\PrettyPageHandler());
		$this->handler->register();
	}

	public function setTitle($title = '')
	{
		$this->handler->setPageTitle($title); return $this;
	}

	public function setEditor($editor = 'sublime')
	{
		$this->handler->setEditor($editor); return $this;
	}

	public function setData($name = '', $data = [])
	{
		$this->handler->addDataTable($name, $data); return $this;
	}

	public function throw($message)
	{
		throw new \RuntimeException($message);
	}
}