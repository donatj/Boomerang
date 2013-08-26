<?php

namespace Boomerang\Runner;

use Boomerang\Boomerang;
use Boomerang\Exceptions\ExpectFailedException;
use CLI\Output;
use CLI\Style;

class UserInterface {

	static function dumpOptions() {
		$fname = Boomerang::$pathInfo['basename'];

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
		Output::string(Boomerang::$pathInfo['basename'] . ": " . $text . PHP_EOL);
		die($code);
	}

}