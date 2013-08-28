<?php

namespace Boomerang\Runner;

use Boomerang\Boomerang;
use CLI\Output;
use CLI\Style;

class UserInterface {

	public function __construct( $STDOUT, $STDERR ) {
		Output::$stream = $STDOUT;
	}

	public function dumpOptions() {
		$fname = Boomerang::$pathInfo['basename'];

		$options = <<<EOT
usage: {$fname} [switches] <directory>
       {$fname} [switches] [APISpec]


EOT;
		Output::string($options);
		Output::string(PHP_EOL);
		die(1);
	}

	public function updateExpectationDisplay( $file ) {
		$validators = Boomerang::popValidators();

		$messages = array();

		foreach( $validators as $validator ) {
			foreach( $validator->getExpectationResults() as $expectationResult ) {
				if( $expectationResult->getFail() ) {
					Output::string(Style::red("F"));
					$messages[] = $expectationResult;
				} else {
					Output::string(Style::green("."));
				}
			}
		}

		if( $messages ) {
			Output::string(PHP_EOL . PHP_EOL);

			foreach( $messages as $expectationResult ) {
				Output::string($file . ' ');
				Output::string("[ " . Style::red($expectationResult->getValidator()->getResponse()->getRequest()->getEndpoint(), 'underline') . " ]" . PHP_EOL);
				Output::string($expectationResult->getMessage() . PHP_EOL . PHP_EOL);

				$actual = $expectationResult->getActual();
				if( $expectationResult->getActual() !== null ) {
					Output::string("Actual: " . PHP_EOL);
					Output::string(var_export($actual, true));
					Output::string(PHP_EOL . PHP_EOL);
				}

				$expected = $expectationResult->getExpected();
				if( $expected !== null ) {
					Output::string("Expected: " . PHP_EOL);
					Output::string(Style::red(var_export($expected, true)));
				}

				Output::string(PHP_EOL . PHP_EOL . Style::light_gray("# " . str_repeat('-', 25)) . PHP_EOL . PHP_EOL);
			}
		}
	}

	public function dropError( $text, $code = 1 ) {
		Output::string(Boomerang::$pathInfo['basename'] . ": " . $text . PHP_EOL);
		die($code);
	}

	public function outputMsg( $text ) {
		Output::string($text . PHP_EOL);
	}

}