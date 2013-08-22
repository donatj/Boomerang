<?php

namespace Phrisby\Runner;

use CLI\Output;

class UserInterface {

	static $phrisbyPath;
	static $pathInfo;

	static function main( $args ) {
		self::$phrisbyPath = realpath($args[0]);
		self::$pathInfo    = pathinfo(self::$phrisbyPath);

		if( count($args) < 2 ) {
			self::dumpOptions();
		}

		$runner = new TestRunner(end($args));
		$runner->runTests();

	}

	static function dumpOptions() {
		Output::string("usage: " . self::$pathInfo['basename'] . " [switches] <directory>" . PHP_EOL);
		Output::string("       " . self::$pathInfo['basename'] . " [switches] [APISpec]" . PHP_EOL . PHP_EOL);
		Output::string(PHP_EOL);
		die(1);
	}

	static function dropError( $text, $code = 1 ) {
		Output::string(self::$pathInfo['basename'] . ": " . $text . PHP_EOL);
		die($code);
	}

}