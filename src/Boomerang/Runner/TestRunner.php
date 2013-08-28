<?php

namespace Boomerang\Runner;

use Boomerang\Boomerang;

class TestRunner {

	private $path;
	private $files;
	/**
	 * @var UserInterface
	 */
	private $ui;

	function __construct( $path, UserInterface $ui ) {
		$this->path  = $path;
		$this->ui    = $ui;
		$this->files = $this->getFileList($this->path);
	}

	/**
	 * @param $path
	 * @return \Iterator
	 */
	function getFileList( $path ) {
		if( $real = realpath($path) ) {
			$path = $real;
		}

		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if( is_dir($path) ) {
			$dir   = new \RecursiveDirectoryIterator($path);
			$ite   = new \RecursiveIteratorIterator($dir);
			$files = new \RegexIterator($ite, "/Spec\.php$/");

			return $files;
		} elseif( is_readable($path) ) {
			return new \ArrayIterator(array( $path ));
		}


		$this->ui->dropError("Cannot find file \"$path\"");

	}

	/**
	 * @param callable $afterExecution
	 */
	function runTests( \Closure $afterExecution = null ) {
		$scope = function ( $file ) { require($file); };

		foreach( $this->files as $file ) {
			$scope($file);

			if($afterExecution !== null) {
				$afterExecution($file);
			}
		}
	}

}