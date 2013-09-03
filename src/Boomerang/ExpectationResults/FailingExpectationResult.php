<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\Validator;

class FailingExpectationResult extends FailingResult {

	/**
	 * @var null|string
	 */
	protected $expected;
	/**
	 * @var null|string
	 */
	protected $actual;

	public function __construct( Validator $validator, $message = null, $expected = null, $actual = null ) {
		parent::__construct($validator, $message);
		$this->expected  = $expected;
		$this->actual    = $actual;
	}

	/**
	 * @return null|string
	 */
	public function getActual() {
		return $this->actual;
	}

	/**
	 * @return null|string
	 */
	public function getExpected() {
		return $this->expected;
	}

}