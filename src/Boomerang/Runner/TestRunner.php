<?php

namespace Boomerang\Runner;

use Boomerang\Exceptions\CliRuntimeException;

class TestRunner {

	/** @var \Iterator */
	private $files;
	private string $path;
	private string $bootstrap;

	/**
	 * TestRunner constructor.
	 */
	public function __construct( string $path, string $bootstrap ) {
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

	public function runTests( ?\Closure $afterExecution = null ) : void {
		if( $this->bootstrap ) {
			if( is_readable($this->bootstrap) ) {
				require_once $this->bootstrap;
			} else {
				throw new CliRuntimeException("Failed to load bootstrap");
			}
		}

		$scope = static fn ( string $file ) => require $file;

		foreach( $this->files as $file ) {
			$scope($file);

			if( $afterExecution !== null ) {
				$afterExecution($file);
			}
		}
	}

}
