<?php

namespace Boomerang;

use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\Runner\TestRunner;
use Boomerang\Runner\UserInterface;
use donatj\Flags;

class Boomerang {

	const VERSION     = ".0.1a";
	const PHAR_URL    = "http://boomerang.donatstudios.com/builds/dev/boomerang.phar";
	const CONFIG_FILE = "boomerang.ini";

	public static $boomerangPath;
	public static $pathInfo;

	private static $bootstrap;
	private static $verbosity;

	/**
	 * @var ValidatorInterface[]
	 */
	public static $validators = array();

	private static function init( $args, UserInterface $ui ) {

		if( file_exists(self::CONFIG_FILE) && is_readable(self::CONFIG_FILE) ) {
			$config = parse_ini_file(self::CONFIG_FILE, true);
		} else {
			$config = array();
		}

		$testSuite = 'default';

		if( isset($config[$testSuite]) ) {
			$suite = $config[$testSuite];

			if( isset($suite['define']) && is_array($suite['define']) ) {
				foreach( $suite['define'] as $key => $definition ) {
					define($key, $definition);
				}
			}
		}

		$flags = new Flags();

		self::$bootstrap = & $flags->string('bootstrap', isset($suite['bootstrap']) ? $suite['bootstrap'] : false, 'A "bootstrap" PHP file that is run before the specs.');
		self::$verbosity = & $flags->short('v', 'Output in verbose mode');

		$displayHelp    = & $flags->bool('help', false, 'Display this help message.');
		$displayVersion = & $flags->bool('version', false, 'Display this applications version.');

		if( defined('BOOMERANG_IS_PHAR') ) {
			$selfUpdate = & $flags->bool('selfupdate', false, 'Update to the latest version of Boomerang!');
		}

		try {
			$flags->parse($args);
		} catch(\Exception $e) {
			$ui->dropError($e->getMessage(), 1, $flags->getDefaults());
		}

		switch( true ) {
			case isset($selfUpdate) && $selfUpdate:
				self::selfUpdate($ui);
				die(0);
				break;
			case $displayVersion:
				self::versionMarker($ui);
				die(0);
				break;
			case $displayHelp:
			case count($flags->args()) < 1: //should come last because of this
				$ui->dumpOptions($flags->getDefaults());
				die(1);
				break;
		}

		if( $flags->args() ) {
			return $flags->args();
		} elseif( isset($flags['paths']) ) {
			return $flags['paths'];
		} else {
			return array();
		}
	}

	static function main( $args ) {
		$ui = new UserInterface(STDOUT, STDERR);

		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo      = pathinfo(self::$boomerangPath);

		$scan = self::init($args, $ui);

		self::versionMarker($ui);

		$runner = new TestRunner(end($scan), self::$bootstrap, $ui);

		$verbosity = self::$verbosity;
		$displayed = array();
		$runner->runTests(function ( $file ) use ( $ui, $verbosity, &$displayed ) {
			$validators = array();
			foreach( Boomerang::$validators as $validator ) {
				$hash = spl_object_hash($validator);

				if( !isset($displayed[$hash]) ) {
					$validators[]     = $validator;
					$displayed[$hash] = true;
				}
			}

			$ui->updateExpectationDisplay($file, $validators, $verbosity);
		});

		$tests = 0;
		$total = 0;
		$fails = 0;
		foreach( Boomerang::$validators as $v_data ) {
			$tests++;
			$ex_res = $v_data->getExpectationResults();
			foreach( $ex_res as $ex ) {
				$total++;
				$fails += $ex->getFail();
			}
		}

		$currMen = number_format(memory_get_usage() / 1048576, 2);
		$peakMem = number_format(memory_get_peak_usage() / 1048576, 2);

		$ui->outputMsg(PHP_EOL);
		$ui->outputMsg("{$tests} tests, {$total} assertions, {$fails} failures, [{$currMen}]{$peakMem}mb peak memory use");

		die($fails ? 2 : 0);
	}

	private static function selfUpdate( UserInterface $ui ) {
		$ui->outputMsg("Starting self update ... ");

		$localFile = $_SERVER['argv'][0];
		$tmpFile   = $localFile . '.tmp.' . microtime();

		if( !is_writable($tmpDir = dirname($tmpFile)) ) {
			$ui->dropError("Update failed: {$tmpDir} not writable.");
		}

		if( !is_writable($localFile) ) {
			$ui->dropError("Update failed: {$localFile} not writable.");
		}

		$pharContent = @file_get_contents(self::PHAR_URL);
		if( !$pharContent ) {
			$ui->dropError('Error downloading PHAR');
		}

		$written = @file_put_contents($tmpFile, $pharContent);
		if( !$written ) {
			$ui->dropError('Error writing PHAR');
		}

		try {
			$phar = new \Phar($tmpFile);
			unset($phar);
		} catch(\Exception $e) {
			unlink($tmpFile);
			$ui->dropError('Download is corrupted. Try re-running selfupdate.');
		}

		chmod($tmpFile, 0777 & ~umask());

		if( !@rename($tmpFile, $localFile) ) {
			$ui->dropError('Failed to replace URL');
		}

		$ui->outputMsg("Success!");
	}

	private static function versionMarker( UserInterface $ui ) {
		$ui->outputMsg("Boomerang! " . self::VERSION . " by Jesse G. Donat" . PHP_EOL);
	}

	/**
	 * @param ValidatorInterface $validator
	 */
	public static function addValidator( ValidatorInterface $validator ) {
		self::$validators[] = $validator;
	}

}