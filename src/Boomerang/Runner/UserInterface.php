<?php

namespace Boomerang\Runner;

use Boomerang\Exceptions\ExpectFailedException;
use CLI\Output;
use CLI\Style;

class UserInterface {

	static $boomerangPath;
	static $pathInfo;

	static function main( $args ) {
		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo      = pathinfo(self::$boomerangPath);

		if( count($args) < 2 ) {
			self::dumpOptions();
		}

		$runner = new TestRunner(end($args));
		$runner->runTests();

		Output::string(PHP_EOL . PHP_EOL);

	}

	static function dumpOptions() {
		$fname = self::$pathInfo['basename'];

		$options = <<<EOT
usage: {$fname} [switches] <directory>
       {$fname} [switches] [APISpec]


EOT;
		Output::string($options);
		Output::string(PHP_EOL);
		die(1);
	}

	static function displayException( ExpectFailedException $ex ) {
		Output::string("[ " . Style::red($ex->getTest()->getRequest()->getEndpoint()) . " ]");
		Output::string(PHP_EOL);;
		Output::string($ex->getMessage());
		Output::string(PHP_EOL . PHP_EOL);;
	}

	static function dropError( $text, $code = 1 ) {
		Output::string(self::$pathInfo['basename'] . ": " . $text . PHP_EOL);
		die($code);
	}

}