<?php

namespace Boomerang\Runner;

class TestRunner {

	private $path;
	/** @var \Iterator */
	private $files;
	private $bootstrap;

	/**
	 * @var UserInterface
	 */
	private $ui;

	/**
	 * TestRunner constructor.
	 *
	 * @param string                          $path
	 * @param string                          $bootstrap
	 * @param \Boomerang\Runner\UserInterface $ui
	 */
	public function __construct( $path, $bootstrap, UserInterface $ui ) {
		$this->path      = $path;
		$this->bootstrap = $bootstrap;
		$this->ui        = $ui;
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
			$files = new \RegexIterator($ite, "/Spec\.php$/");

			return $files;
		} elseif( is_readable($path) ) {
			return new \ArrayIterator(array( $path ));
		}


		$this->ui->dropError("Cannot find file \"$path\"");
	}

	/**
	 * @param \Closure $afterExecution
	 */
	public function runTests( \Closure $afterExecution = null ) {
		if( $this->bootstrap ) {
			if( is_readable($this->bootstrap) ) {
				require_once($this->bootstrap);
			} else {
				/**
				 * @todo replace UI drop errors with throwing exceptions
				 */
				$this->ui->dropError("Failed to load bootstrap");
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