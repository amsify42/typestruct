<?php

namespace Amsify42\TypeStruct\Core;

use stdClass;

class Resource
{
	/**
	 * Generated typestruct info 
	 * @var array
	 */
	protected $gInfo = [];

	public function __construct()
	{
		if(!defined('TS_SRC_PATH')) define('TS_SRC_PATH', __DIR__.'/..');
	}

	/**
	 * Get Autoload Psr4 Path
	 * @param  string $class [full name of class with namespace]
	 * @return string        [file path of class based on psr4 autoloading]
	 */
	protected function getAutoloadPsr4Path(string $class): ?string
	{
		$classPath 	= NULL;
		$appLevel 	= false;
		$directory 	= TS_SRC_PATH.'/../';
		if(is_dir(TS_SRC_PATH.'/../../../../vendor')) {
			$appLevel 	= true;
			$directory 	= TS_SRC_PATH.'/../../../../';
		}
		if(is_file($directory.'composer.json')) {
			$composer 	= json_decode(file_get_contents($directory.'composer.json'), true);
			if($composer) {
				if($composer['autoload'] && $composer['autoload']['psr-4']) {
					$classPath = $this->isClassPath($composer['autoload']['psr-4'], $class, $directory);
				}
				if(!$classPath && $composer['autoload-dev'] && $composer['autoload-dev']['psr-4']) {
					$classPath = $this->isClassPath($composer['autoload-dev']['psr-4'], $class, $directory);
				}
			}
		}
		if($appLevel && !$classPath) {
			$directory 	= TS_SRC_PATH.'/../';
			if(is_file($directory.'composer.json')) {
				$composer 	= json_decode(file_get_contents($directory.'composer.json'), true);
				if($composer) {
					if($composer['autoload'] && $composer['autoload']['psr-4']) {
						$classPath = $this->isClassPath($composer['autoload']['psr-4'], $class, $directory);
					}
					if(!$classPath && $composer['autoload-dev'] && $composer['autoload-dev']['psr-4']) {
						$classPath = $this->isClassPath($composer['autoload-dev']['psr-4'], $class, $directory);
					}
				}
			}
		}
		return $classPath;
	}

	/**
	 * Checks whether the class name contains the psr4 namespace which is defined in composer.json
	 * @param  array  	$namespaces
	 * @param  string  	$class
	 * @param  string  	$directory
	 * @return string
	 */
	private function isClassPath(array $namespaces, string $class, string $directory): ?string
	{
		$classPath = NULL;
		foreach($namespaces as $namespace => $dir) {
			if(strpos($class, $namespace) !== false) {
				$classFile 	= str_replace($namespace, '', $class);
				$classFile 	= str_replace('\\', '/', $classFile);
				$classFile 	= $classFile.'.php';
				$path 		= $directory.$dir.$classFile;
				if(file_exists($path)) {
					$classPath = $path; break;
				}
			}
		}
		return $classPath;
	}

	/**
	 * Generate directory and class for given struct
	 * @return void
	 */
	protected function generateStruct(): void
	{
		$this->generateDirectory();
		$this->generateJson();
	}

	/**
	 * Generate Directory for typestruct class
	 * @return void
	 */
	private function generateDirectory(): void
	{
		$this->gInfo['name'] 	= md5($this->info['full_name']);
		$this->gInfo['dir'] 	= resource('generate/'.$this->gInfo['name']);
		if(!file_exists($this->gInfo['dir'])) {
		    mkdir($this->gInfo['dir'], 0777, true);
		}
		$this->gInfo['json'] 	= $this->gInfo['dir'].'/info.json';
		$this->gInfo['php'] 	= $this->gInfo['dir'].'/'.$this->info['name'].'.php';
	}

	/**
	 * Generate Json for storing updated time of typestruct file
	 * @return void
	 */
	private function generateJson(): void
	{
		$getFile = json_decode($this->getJsonFile());
		$updated = filemtime($this->info['path']);
		if(!$getFile || ($getFile->updated != $updated) || ($getFile->isFull != $this->validateFull)) {
			$data 			= new stdClass();
			$data->updated 	= $updated;
			$data->isFull 	= $this->validateFull;
			$jsonData 		= json_encode($data);
			file_put_contents($this->gInfo['json'], $jsonData);
			$this->generateClass();
		}
	}

	/**
	 * Generate typestruct equivalent class
	 * @return void
	 */
	private function generateClass(): void
	{
		$fp 		= fopen($this->gInfo['php'], 'w');
		$isFull 	= ($this->validateFull)? 'true': 'false';
		$content 	= "<?php";
		if(isset($this->info['namespace']) && $this->info['namespace']) {
			$content .= " 

namespace {$this->info['namespace']};";
		}
		$content .= "

class {$this->info['name']} extends \Amsify42\TypeStruct\DataType\Struct
{
	protected \$structure = '".base64_encode(serialize($this->structure))."';

	function __construct(\$data)
	{
		parent::__construct(\$data, unserialize(base64_decode(\$this->structure)), {$isFull});
	}
}";
		fwrite($fp, $content);
		fclose($fp);
	}

	/**
	 * Get Json file if existing
	 * @return jsonString|NULL
	 */
	private function getJsonFile()
	{
		return (is_file($this->gInfo['json']))? file_get_contents($this->gInfo['json']): NULL;
	}

}