<?php

namespace Boomerang\Runner;

use Boomerang\Boomerang;
use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\InfoResult;
use Boomerang\ExpectationResults\PassingExpectationResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ResponseValidatorInterface;
use Boomerang\Interfaces\ValidatorInterface;
use CLI\Output;
use CLI\Style;

class UserInterface {

	public function __construct( $STDOUT, $STDERR ) {
		Output::$stream = $STDOUT;
	}

	public function dumpOptions( $additional ) {
		$fname = Boomerang::$pathInfo['basename'];

		$options = <<<EOT
usage: {$fname} [switches] <directory>
       {$fname} [switches] [APISpec]


EOT;

		Output::string($options);
		Output::string($additional);
		Output::string(PHP_EOL);
	}

	/**
	 * @param string               $file
	 * @param ValidatorInterface[] $validators
	 * @param bool                 $verbose
	 */
	public function updateExpectationDisplay( $file, $validators, $verbose = false ) {

		foreach( $validators as $validator ) {
			if( $validator instanceof ValidatorInterface ) {
				$dot = false;

				foreach( $validator->getExpectationResults() as $expectationResult ) {
					if( $expectationResult instanceof FailingResult ) {
						$dot = Style::red("F");
					} elseif( $expectationResult instanceof InfoResult ) {
						$dot = Style::normal("I");
					} elseif( !$expectationResult instanceof PassingResult ) {
						$dot = Style::red("?");
					}

					if( $dot ) {
						break;
					}
				}

				Output::string($dot ? : Style::green("."));
			} else {
				$this->dropError("Error: Unexpected ValidatorInterface", E_USER_ERROR);
			}
		}

		$lastEndpoint      = false;
		$fileDisplayed     = false;
		$initialWhitespace = false;

		foreach( $validators as $validator ) {

			foreach( $validator->getExpectationResults() as $expectationResult ) {
				if( $expectationResult instanceof ExpectationResultInterface ) {
					if( !($expectationResult instanceof PassingResult) || $verbose ) {

						if( !$initialWhitespace ) {
							Output::string(PHP_EOL);
							$initialWhitespace = true;
						}


						if( $validator instanceof ResponseValidatorInterface ) {
							$endpoint = $validator->getResponse()->getRequest()->getEndpoint();
						} else {
							$endpoint = false;
						}

						Output::string(PHP_EOL . Style::light_gray("# " . str_repeat('-', 25)) . PHP_EOL . PHP_EOL);

						if( !$fileDisplayed ) {
							Output::string(Style::red($file) . PHP_EOL);
							$fileDisplayed = true;
						}

						if( $endpoint && $endpoint != $lastEndpoint ) {
							Output::string("[ " . Style::blue($endpoint, 'underline') . " ]" . PHP_EOL . PHP_EOL);
						}

						Output::string($expectationResult->getMessage() . PHP_EOL . PHP_EOL);

						if( $expectationResult instanceof FailingExpectationResult ) {
							$actual   = $expectationResult->getActual();
							$expected = $expectationResult->getExpected();

							if( $expectationResult->getActual() !== null ) {
								Output::string("Actual: " . PHP_EOL);
								Output::string(var_export($actual, true));
								Output::string(PHP_EOL);
							}

							if( $expected !== null ) {
								Output::string(PHP_EOL);
								Output::string("Expected: " . PHP_EOL);
								Output::string(Style::red(var_export($expected, true)));
								Output::string(PHP_EOL);
							}

						} elseif( $expectationResult instanceof PassingExpectationResult ) {
							$actual = $expectationResult->getActual();

							if( $expectationResult->getActual() !== null ) {
								Output::string("Actual as Expected: " . PHP_EOL);
								Output::string(Style::green(var_export($actual, true)));
								Output::string(PHP_EOL);
							}
						}

						$lastEndpoint = $endpoint;
					}

				} elseif( is_string($expectationResult) ) {
					$this->outputMsg('MSG: ' . $expectationResult);
				} else {
					$this->outputMsg("Error: Unexpected Expectation:" . var_export($expectationResult, true));
				}
			}
		}
	}

	public function dropError( $text, $code = 1, $additional = false ) {
		Output::string(Boomerang::$pathInfo['basename'] . ": " . Style::red($text) . PHP_EOL . ($additional ? $additional . PHP_EOL : ''));
		die($code);
	}

	public function outputMsg( $text ) {
		Output::string($text . PHP_EOL);
	}

}