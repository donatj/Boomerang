<?php

namespace Boomerang\Runner;

use Boomerang\Exceptions\CliRuntimeException;

class TestRunner {

	/** @var \Iterator<int|string, string|\SplFileInfo> */
	private \Iterator $files;
	/** @var string */
	private string $path;
	/** @var string|null */
	private ?string $bootstrap;

	/**
	 * TestRunner constructor.
	 *
	 * @param string       $path
	 * @param string|false|null $bootstrap
	 */
	public function __construct( $path, $bootstrap ) {
		$this->path      = $path;
		$this->bootstrap = is_string($bootstrap) ? $bootstrap : null;
		$this->files     = $this->getFileList($this->path);
	}

	/**
	 * @param string $path
	 * @return \Iterator<int|string, string|\SplFileInfo>
	 */
	private function getFileList( string $path ) {
		if( $real = realpath($path) ) {
			$path = $real;
		}

		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if( is_dir($path) ) {
			$dir = new \RecursiveDirectoryIterator($path);
			$ite = new \RecursiveIteratorIterator($dir);

			return new \RegexIterator($ite, "/Spec\.php$/");
		}

		if( is_readable($path) ) {
			return new \ArrayIterator([ $path ]);
		}

		throw new CliRuntimeException("Cannot find file \"$path\"");
	}

	/**
	 * @param \Closure|null $afterExecution
	 */
	public function runTests( ?\Closure $afterExecution = null ) {
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
