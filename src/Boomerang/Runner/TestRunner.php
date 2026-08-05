<?php

namespace Boomerang\Runner;

use Boomerang\Exceptions\CliRuntimeException;

class TestRunner {

	/** @var \Iterator<int|string, \SplFileInfo|string> */
	private \Iterator $files;

	private ?string $bootstrap;

	/**
	 * TestRunner constructor.
	 *
	 * @param array<int|string, string>|string $paths
	 */
	public function __construct( $paths, ?string $bootstrap ) {
		$this->bootstrap = is_string($bootstrap) ? $bootstrap : null;
		$this->files     = $this->getFileListForPaths(is_array($paths) ? $paths : [ $paths ]);
	}

	/**
	 * @param array<int|string, string> $paths
	 * @return \Iterator<int|string, \SplFileInfo|string>
	 */
	private function getFileListForPaths( array $paths ) : \Iterator {
		foreach( $paths as $path ) {
			yield from $this->getFileList($path);
		}
	}

	/**
	 * @return \Iterator<int|string, \SplFileInfo|string>
	 */
	private function getFileList( string $path ) : \Iterator {
		if( $real = realpath($path) ) {
			$path = $real;
		}

		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if( is_dir($path) ) {
			$dir = new \RecursiveDirectoryIterator($path);
			$ite = new \RecursiveIteratorIterator($dir);

			return new \RegexIterator($ite, "/Spec\\.php$/");
		}

		if( is_readable($path) ) {
			return new \ArrayIterator([ $path ]);
		}

		throw new CliRuntimeException("Cannot find file \"$path\"");
	}

	public function runTests( ?\Closure $afterExecution = null ) {
		if( $this->bootstrap ) {
			if( is_readable($this->bootstrap) ) {
				require_once $this->bootstrap;
			} else {
				throw new CliRuntimeException("Failed to load bootstrap");
			}
		}

		$scope = function ( $file ) { require $file; };

		foreach( $this->files as $file ) {
			$scope($file);

			if( $afterExecution !== null ) {
				$afterExecution($file);
			}
		}
	}

}
