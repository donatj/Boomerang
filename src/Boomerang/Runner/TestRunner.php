<?php

namespace Boomerang\Runner;

use Boomerang\Exceptions\CliRuntimeException;

class TestRunner {

	/** @var \Iterator */
	private $files;
	private $path;
	private $bootstrap;

	/**
	 * TestRunner constructor.
	 *
	 * @param string $path
	 * @param string $bootstrap
	 */
	public function __construct( $path, $bootstrap ) {
		$this->path      = $path;
		$this->bootstrap = $bootstrap;
		$this->files     = $this->getFileList($this->path);
	}

	/**
	 * @param $path
	 * @return \Iterator
	 */
	private function getFileList( $path ) {
		if( $real = realpath($path) ) {
			$path = $real;
		}

		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if( is_dir($path) ) {
			$dir   = new \RecursiveDirectoryIterator($path);
			$ite   = new \RecursiveIteratorIterator($dir);

			return new \RegexIterator($ite, "/Spec\\.php$/");
		}
		if( is_readable($path) ) {
			return new \ArrayIterator([ $path ]);
		}

		throw new CliRuntimeException("Cannot find file \"$path\"");
	}

	/**
	 * @param \Closure $afterExecution
	 */
	public function runTests( \Closure $afterExecution = null ) {
		if( $this->bootstrap ) {
			if( is_readable($this->bootstrap) ) {
				require_once($this->bootstrap);
			} else {
				throw new CliRuntimeException("Failed to load bootstrap");
			}
		}

		$scope = function ( $file ) { require($file); };

		foreach( $this->files as $file ) {
			$scope($file);

			if( $afterExecution !== null ) {
				$afterExecution($file);
			}
		}
	}

}
