<?php

namespace Boomerang\Runner;

use Boomerang\Boomerang;
use Boomerang\Exceptions\CliRuntimeException;
use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\InfoResult;
use Boomerang\ExpectationResults\MutedExpectationResult;
use Boomerang\ExpectationResults\PassingExpectationResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\HttpResponseInterface;
use Boomerang\Interfaces\ResponseValidatorInterface;
use Boomerang\Interfaces\ValidatorInterface;
use CLI\Output;
use CLI\Style;

class UserInterface {

	/** @var resource */
	protected $stdout;
	/** @var resource */
	protected $stderr;

	/**
	 * UserInterface constructor.
	 *
	 * @param resource $STDOUT
	 * @param resource $STDERR
	 */
	public function __construct( $STDOUT, $STDERR ) {
		$this->stdout   = $STDOUT;
		$this->stderr   = $STDERR;
		Output::$stream = $STDOUT;
	}

	public function dumpOptions( string $additional ) : void {
		$base = Boomerang::$pathInfo['basename'];

		$options = <<<EOT
			usage: {$base} [switches] <directory>
			       {$base} [switches] [APISpec]


			EOT;

		Output::string($options);
		Output::string($additional);
		Output::string(PHP_EOL);
	}

	/**
	 * @param ValidatorInterface[] $validators
	 */
	public function updateExpectationDisplay( string $file, array $validators, int $verbose = 0 ) : void {

		foreach( $validators as $validator ) {
			if( !$validator instanceof ValidatorInterface ) {
				throw new CliRuntimeException("Error: Unexpected ValidatorInterface");
			}

			$dot = false;

			foreach( $validator->getExpectationResults() as $expectationResult ) {
				if( $expectationResult instanceof FailingResult ) {
					$dot = Style::red("F");
				} elseif( $expectationResult instanceof InfoResult ) {
					$dot = Style::normal("I");
				} elseif( $expectationResult instanceof MutedExpectationResult ) {
					$dot = false;
				} elseif( !$expectationResult instanceof PassingResult ) {
					$dot = Style::red("?");
				}

				if( $dot ) {
					break;
				}
			}

			if( !$verbose ) {
				Output::string($dot ?: Style::green("."));
			}
		}

		$lastEndpoint      = false;
		$fileDisplayed     = false;
		$initialWhitespace = false;

		foreach( $validators as $validator ) {

			foreach( $validator->getExpectationResults() as $expectationResult ) {

				if( $expectationResult instanceof MutedExpectationResult && $verbose < 2 ) {
					continue;
				}

				if( !$expectationResult instanceof ExpectationResultInterface ) {
					if( is_string($expectationResult) ) {
						$this->outputMsg('MSG: ' . $expectationResult);
					} else {
						$this->outputMsg("Error: Unexpected Expectation:" . var_export($expectationResult, true));
					}

					continue;
				}

				$notPassing = !($expectationResult instanceof PassingResult);
				if( $notPassing || $verbose ) {

					if( !$initialWhitespace ) {
						Output::string(PHP_EOL);
						$initialWhitespace = true;
					}

					$endpoint          = false;
					$validatorResponse = null;
					$validatorRequest  = null;
					if( $validator instanceof ResponseValidatorInterface ) {
						$validatorResponse = $validator->getResponse();
						if( $validatorResponse instanceof HttpResponseInterface ) {
							$validatorRequest = $validatorResponse->getRequest();
							if( $validatorRequest ) {
								$endpoint = $validatorRequest->getEndpoint();
							}
						}
					}

					if( $notPassing || $verbose > 1 ) {
						Output::string(PHP_EOL . Style::light_gray("# " . str_repeat('-', 25)) . PHP_EOL);
					}

					if( !$fileDisplayed ) {
						Output::string(Style::red($file) . PHP_EOL);
						$fileDisplayed = true;
					}

					if( $endpoint && $endpoint !== $lastEndpoint ) {
						Output::string("[ " . Style::blue($endpoint, 'underline') . " ]");
						if( $verbose ) {
							Output::string('( ' . $validatorRequest->getLastRequestTime() . 's )');
						}

						Output::string(PHP_EOL);
					}

					if( $notPassing || $verbose > 1 ) {
						Output::string(PHP_EOL . $expectationResult->getMessage() . PHP_EOL . PHP_EOL);

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

							if( $verbose > 2 ) {
								if( $expectationResult->getActual() !== null ) {
									Output::string("Actual as Expected: " . PHP_EOL);
									Output::string(Style::green(var_export($actual, true)));
									Output::string(PHP_EOL);
								}
							}
						}
					}

					$lastEndpoint = $endpoint;
				}
			}
		}
	}

	public function dropError( string $text, int $code = 1, ?string $additional = null ) : void {
		Output::$stream = $this->stderr;
		Output::string(Boomerang::$pathInfo['basename'] . ": " . Style::red($text) . PHP_EOL . ($additional ? $additional . PHP_EOL : ''));

		die($code);
	}

	public function outputMsg( string $text ) : void {
		Output::string($text . PHP_EOL);
	}

}
