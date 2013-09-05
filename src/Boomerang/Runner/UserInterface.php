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

	public function updateExpectationDisplay( $file, $validators, $verbose = false ) {
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
						Output::string(Style::normal("I"));
					} else {
						Output::string(Style::red("?"));
					}
				}
			} else {
				$messages[] = "Error: Unexpected Validator:" . var_export($validator, true);
			}
		}


		$lastEndpoint = false;

		$fileDisplayed = false;

		foreach( $validators as $validator ) {
			if( $validator instanceof Validator ) {
				foreach( $validator->getExpectationResults() as $expectationResult ) {


					if( $expectationResult instanceof ExpectationResult ) {

						if( !($expectationResult instanceof PassingResult)  ) {

							$endpoint = $expectationResult->getValidator()->getResponse()->getRequest()->getEndpoint();

							Output::string(PHP_EOL . PHP_EOL);

							if( !$fileDisplayed ) {
								Output::string(Style::red($file) . PHP_EOL . PHP_EOL);
								$fileDisplayed = true;
							}

							if( $endpoint != $lastEndpoint ) {
								Output::string("[ " . Style::blue($endpoint, 'underline') . " ]" . PHP_EOL . PHP_EOL);
							}

							Output::string($expectationResult->getMessage() . PHP_EOL . PHP_EOL);

							if( $expectationResult instanceof FailingExpectationResult ) {
								$actual   = $expectationResult->getActual();
								$expected = $expectationResult->getExpected();

								if( $expectationResult->getActual() !== null ) {
									Output::string("Actual: " . PHP_EOL);
									Output::string(var_export($actual, true));
									Output::string(PHP_EOL . PHP_EOL);
								}

								if( $expected !== null ) {
									Output::string("Expected: " . PHP_EOL);
									Output::string(Style::red(var_export($expected, true)));
								}
							}

							Output::string(PHP_EOL . PHP_EOL . Style::light_gray("# " . str_repeat('-', 25)) . PHP_EOL . PHP_EOL);

							$lastEndpoint = $endpoint;
						}

					} elseif( is_string($expectationResult) ) {
						$this->outputMsg('MSG: ' . $expectationResult);
					} else {
						$this->outputMsg("Error: Unexpected Expectation:" . var_export($expectationResult, true));
					}

				}
			} else {
				$this->dropError("Error: Unexpected Validator", E_USER_ERROR);
			}
		}

//		if( $messages ) {
//			Output::string(PHP_EOL . PHP_EOL);
//			$lastFile     = false;
//			$lastEndpoint = false;
//
//			foreach( $messages as $expectationResult ) {
//
//
//			}
//		}
	}

	public
	function outputMsg( $text ) {
		Output::string($text . PHP_EOL);
	}

	public
	function dropError( $text, $code = 1 ) {
		Output::string(Boomerang::$pathInfo['basename'] . ": " . $text . PHP_EOL);
		die($code);
	}

}