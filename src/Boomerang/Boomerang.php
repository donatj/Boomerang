<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;
use Boomerang\Runner\TestRunner;
use Boomerang\Runner\UserInterface;
use CLI\Output;

class Boomerang {

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

		$runner = new TestRunner(end($args), $ui);
		$runner->runTests();

		Output::string(PHP_EOL . PHP_EOL);

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