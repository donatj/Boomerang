<?php

namespace Boomerang\Runner;

use Boomerang\Exceptions\CliRuntimeException;

class TestRunner {

	private iterable $files;
	private ?string $bootstrap;

	/**
	 * TestRunner constructor.
	 */
	public function __construct( array $paths, ?string $bootstrap ) {
		$this->bootstrap = $bootstrap;
		$this->files     = $this->getFileListForPaths($paths);
	}

	private function getFileListForPaths( array $paths ) : iterable {
		foreach( $paths as $path ) {
			yield from $this->getFileList($path);
		}
	}

	private function getFileList( string $path ) : iterable {
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

	public function runTests() : \Generator {
		if( $this->bootstrap ) {
			if( !is_readable($this->bootstrap) ) {
				throw new CliRuntimeException("Failed to load bootstrap");
			}

			require_once $this->bootstrap;
		}

		$scope = static fn ( string $file ) => require $file;
		foreach( $this->files as $file ) {
			yield $file => $scope($file);
		}
	}

}
