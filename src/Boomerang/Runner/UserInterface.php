<?php

namespace Boomerang\Runner;

use Boomerang\Boomerang;
use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\InfoResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ExpectationResult;
use Boomerang\Interfaces\Validator;
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

	public function updateExpectationDisplay( $file, $validators ) {
		$messages = array(); //store the ones to display so we can show all the .'s / ?'s first

		foreach( $validators as $validator ) {
			if( $validator instanceof Validator ) {
				foreach( $validator->getExpectationResults() as $expectationResult ) {
					if( $expectationResult instanceof FailingResult ) {
						Output::string(Style::red("F"));
						$messages[] = $expectationResult;
					} elseif( $expectationResult instanceof PassingResult ) {
						Output::string(Style::green("."));
					} elseif( $expectationResult instanceof InfoResult ) {
						$messages[] = $expectationResult;
					} else {
						Output::string(Style::red("?"));
						$messages[] = "Error: Unexpected ExpectationResults:" . var_export($expectationResult, true);
					}
				}
			} else {
				$messages[] = "Error: Unexpected Validator:" . var_export($validator, true);
			}
		}

		if( $messages ) {
			Output::string(PHP_EOL . PHP_EOL);

			foreach( $messages as $expectationResult ) {

				Output::string($file . ' ');

				if( $expectationResult instanceof ExpectationResult ) {

					Output::string("[ " . Style::red($expectationResult->getValidator()->getResponse()->getRequest()->getEndpoint(), 'underline') . " ]" . PHP_EOL);
					Output::string($expectationResult->getMessage() . PHP_EOL . PHP_EOL);

					if( $expectationResult instanceof FailingExpectationResult ) {
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
					}

				} elseif( is_string($expectationResult) ) {
					Output::string($expectationResult);
				} else {

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