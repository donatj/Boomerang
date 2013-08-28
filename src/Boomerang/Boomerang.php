<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;
use Boomerang\Runner\TestRunner;
use Boomerang\Runner\UserInterface;

class Boomerang {

	const VERSION = ".0.1a";

	public static $boomerangPath;
	public static $pathInfo;
	private static $validators = array();

	static function main( $args ) {
		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo      = pathinfo(self::$boomerangPath);


		$ui = new UserInterface(STDOUT, STDERR);

		if( count($args) < 2 ) {
			$ui->dumpOptions();
		}

		$ui->outputMsg("Boomerang " . self::VERSION . " by Jesse G. Donat" . PHP_EOL);

		$runner = new TestRunner(end($args), $ui);

		$runner->runTests(function ( $file ) use ( $ui ) {
			$ui->updateExpectationDisplay($file);
		});

		$ui->outputMsg(PHP_EOL . PHP_EOL);

	}

	/**
	 * @param Validator $validator
	 */
	public static function addValidator( Validator $validator ) {
		self::$validators[] = $validator;
	}

	/**
	 * @return array
	 */
	public static function popValidators() {
		$validators       = self::$validators;
		self::$validators = array();

		return $validators;
	}

}