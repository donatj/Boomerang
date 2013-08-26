<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;
use Boomerang\Runner\TestRunner;
use Boomerang\Runner\UserInterface;
use CLI\Output;

class Boomerang {

	public static $boomerangPath;
	public static $pathInfo;

	public static $validators = array();

	static function main( $args ) {
		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo      = pathinfo(self::$boomerangPath);

		if( count($args) < 2 ) {
			UserInterface::dumpOptions();
		}

		$runner = new TestRunner(end($args));
		$runner->runTests();

		Output::string(PHP_EOL . PHP_EOL);

	}

	public static function addValidator( Validator $validator ) {

	}

}