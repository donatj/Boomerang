<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ValidatorInterface;

class FailingExpectationResult extends FailingResult {

	protected $expected;

	protected $actual;

	/**
	 * @param mixed|null $expected Value that was expected
	 * @param mixed|null $actual   Value that was received
	 */
	public function __construct( ValidatorInterface $validator, ?string $message = null, $expected = null, $actual = null ) {
		parent::__construct($validator, $message);

		$this->expected = $expected;
		$this->actual   = $actual;
	}

	/**
	 * @return mixed|null Value that was expected
	 */
	public function getActual() {
		return $this->actual;
	}

	/**
	 * @return mixed|null Value that was received
	 */
	public function getExpected() {
		return $this->expected;
	}

}
