<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ValidatorInterface;

class FailingExpectationResult extends FailingResult {

	/**
	 * @var mixed
	 */
	protected $expected;
	/**
	 * @var mixed
	 */
	protected $actual;

	/**
	 * @param string|null        $message
	 */
	public function __construct( ValidatorInterface $validator, $message = null, $expected = null, $actual = null ) {
		parent::__construct($validator, $message);
		$this->expected = $expected;
		$this->actual   = $actual;
	}

	public function getActual() {
		return $this->actual;
	}

	public function getExpected() {
		return $this->expected;
	}

}
