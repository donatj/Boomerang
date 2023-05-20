<?php

namespace Boomerang;

use Boomerang\Exceptions\BoomerangException;
use Boomerang\Exceptions\CliExceptionInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\Runner\TestRunner;
use Boomerang\Runner\UserInterface;
use donatj\Flags;

/**
 * Boomerang Application
 */
class Boomerang {

	/** @access private */
	public const VERSION = ".0.2.0";
	/** @access private */
	public const CONFIG_FILES = [".boomerang.ini", "boomerang.ini", ".boomerang.dist.ini", "boomerang.dist.ini"];

	/** @access private */
	public static $boomerangPath;
	/** @access private */
	public static $pathInfo;

	private static string $bootstrap;
	private static int $verbosity;

	/** @var ValidatorInterface[] */
	private static array $validators = [];

	/**
	 * @param string[] $args
	 *@throws \donatj\Exceptions\AbstractFlagException
	 * @return array|string[]
	 */
	private static function init( array $args, UserInterface $ui ) : array {
		$flags     = new Flags;
		$testSuite = &$flags->string('testsuite', 'default', 'Which test suite to run.');
		$flags->parse($args, true);

		foreach(self::CONFIG_FILES as $configFile) {
			if( is_readable($configFile) ) {
				$config = parse_ini_file($configFile, true);
				if( $config === false ) {
					$ui->dropError("Unable to parse config file '{$configFile}'", 2, $flags->getDefaults());
				}

				break;
			}
		}

		if( isset($config[$testSuite]) ) {
			$suite = $config[$testSuite];

			if( isset($suite['define']) && is_array($suite['define']) ) {
				foreach( $suite['define'] as $key => $definition ) {
					define($key, $definition);
				}
			}
		} elseif( $testSuite !== 'default' ) {
			$ui->dropError("testsuite '{$testSuite}' is not known.", 1, $flags->getDefaults());
		}

		if( isset($config['define']) && is_array($config['define']) ) {
			foreach( $config['define'] as $key => $definition ) {
				if( !defined($key) ) {
					define($key, $definition);
				}
			}
		}

		self::$bootstrap = &$flags->string('bootstrap', $suite['bootstrap'] ?? '', 'A "bootstrap" PHP file that is run before the specs.');
		self::$verbosity = &$flags->short('v', 'Output in verbose mode');

		$displayHelp    = &$flags->bool('help', false, 'Display this help message.');
		$displayVersion = &$flags->bool('version', false, 'Display this applications version.');

		try {
			$flags->parse($args);
		} catch( \Exception $e ) {
			$ui->dropError($e->getMessage(), 1, $flags->getDefaults());
		}

		$paths = [];

		if( $flags->args() ) {
			$paths = $flags->args();
		} elseif( isset($suite['paths']) ) {
			$paths = $suite['paths'];
		}

		switch( true ) {
			case $displayVersion:
				self::versionMarker($ui);

				die(0);
			case $displayHelp:
			case count($paths) < 1: // should come last because of this
				$ui->dumpOptions($flags->getDefaults());

				die(1);
		}

		return $paths;
	}

	/**
	 * @access private
	 */
	public static function main( array $args ) : int {
		$start = microtime(true);

		$stdout = fopen('php://stdout', 'w');
		$stderr = fopen('php://stderr', 'w');

		$ui = new UserInterface($stdout, $stderr);

		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo      = pathinfo(self::$boomerangPath);

		$paths = self::init($args, $ui);

		self::versionMarker($ui);

		$tests = 0;
		$total = 0;
		$fails = 0;

		try {
			$runner = new TestRunner($paths, self::$bootstrap);

			$verbosity = self::$verbosity;
			$displayed = [];
			$testRuns = $runner->runTests();
			foreach($testRuns as $file => $_) {
				$validators = [];
				foreach( self::$validators as $validator ) {
					$hash = spl_object_hash($validator);

					if( !isset($displayed[$hash]) ) {
						$validators[]     = $validator;
						$displayed[$hash] = true;
					}
				}

				$ui->updateExpectationDisplay($file, $validators, $verbosity);
			}

			foreach( self::$validators as $v_data ) {
				$tests++;
				$ex_res = $v_data->getExpectationResults();
				foreach( $ex_res as $ex ) {
					$total++;
					$fails += $ex->getFail();
				}
			}
		} catch( CliExceptionInterface $ex ) {
			$ui->dropError($ex->getMessage(), $ex->getExitCode());
		} catch ( BoomerangException $ex ) {
			$ui->dropError($ex->getMessage(), 3);
		}

		$currMen = number_format(memory_get_usage() / 1048576, 2);
		$peakMem = number_format(memory_get_peak_usage() / 1048576, 2);
		$seconds = number_format(microtime(true) - $start, 2);

		$ui->outputMsg(PHP_EOL);
		$ui->outputMsg("{$tests} tests, {$total} assertions, {$fails} failures, [{$currMen}]{$peakMem}mb peak memory use, {$seconds} seconds");

		return $fails ? 2 : 0;
	}

	private static function versionMarker( UserInterface $ui ) : void {
		$ui->outputMsg("Boomerang! " . self::VERSION . " by Jesse G. Donat" . PHP_EOL);
	}

	/**
	 * Register a Validator with Boomerang
	 *
	 * After creating an instance of a Validator, it needs to be registered with Boomerang in order for results to be
	 * tallied and displayed.
	 */
	public static function addValidator( ValidatorInterface $validator ) : void {
		self::$validators[] = $validator;
	}

}
