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
	 * @param ValidatorInterface   $validator
	 * @param null|string $message
	 * @param mixed       $expected
	 * @param mixed       $actual
	 */
	public function __construct( ValidatorInterface $validator, $message = null, $expected = null, $actual = null ) {
		parent::__construct($validator, $message);
		$this->expected = $expected;
		$this->actual   = $actual;
	}

	/**
	 * @return mixed
	 */
	public function getActual() {
		return $this->actual;
	}

	/**
	 * @return mixed
	 */
	public function getExpected() {
		return $this->expected;
	}

}