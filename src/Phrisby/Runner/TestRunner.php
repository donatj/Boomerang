<?php

namespace Phrisby\Runner;

use Phrisby\TestCase;

class TestRunner {

	private $path;
	private $files;

	function __construct($path) {
		$this->path = $path;
		$this->files = $this->getFileList( $this->path );
	}

	function runTests() {
		$scope = function($file) { require($file); };

		foreach( $this->files as $file ) {
			$scope($file);
			if(TestCase::$exceptions) {
				foreach( TestCase::$exceptions as $ex ) {
					UserInterface::displayException( $ex );
				}
			}

			TestCase::$exceptions = array();
		}
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
			return new \ArrayIterator(array($path));
		}

		UserInterface::dropError("Cannot find file \"$path\"");

	}

}