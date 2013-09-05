<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\Validator;

class PassingExpectationResult extends PassingResult {

	/**
	 * @var mixed
	 */
	protected $actual;

	/**
	 * @param Validator   $validator
	 * @param null|string $message
	 * @param mixed       $actual
	 */
	public function __construct( Validator $validator, $message = null, $actual = null ) {
		parent::__construct($validator, $message);
		$this->actual = $actual;
	}

	/**
	 * @return mixed
	 */
	public function getActual() {
		return $this->actual;
	}

}