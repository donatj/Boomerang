<?php

namespace Boomerang\Runner;

use CLI\Output;
use CLI\Style;
use Boomerang\Exceptions\ExpectFailedException;

class UserInterface {

	static $boomerangPath;
	static $pathInfo;

	static function main( $args ) {
		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo    = pathinfo(self::$boomerangPath);

		if( count($args) < 2 ) {
			self::dumpOptions();
		}

		$runner = new TestRunner(end($args));
		$runner->runTests();

		Output::string(PHP_EOL . PHP_EOL);

	}

	static function dumpOptions() {
		Output::string("usage: " . self::$pathInfo['basename'] . " [switches] <directory>" . PHP_EOL);
		Output::string("       " . self::$pathInfo['basename'] . " [switches] [APISpec]" . PHP_EOL . PHP_EOL);
		Output::string(PHP_EOL);
		die(1);
	}

	static function displayException(ExpectFailedException $ex){
		Output::string( "[ " . Style::red( $ex->getTest()->getRequest()->getEndpoint() ) . " ]" );
		Output::string(PHP_EOL);;
		Output::string($ex->getMessage());
		Output::string(PHP_EOL . PHP_EOL);;
	}

	static function dropError( $text, $code = 1 ) {
		Output::string(self::$pathInfo['basename'] . ": " . $text . PHP_EOL);
		die($code);
	}

}