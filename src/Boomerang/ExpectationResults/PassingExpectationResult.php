<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\Validator;

class PassingExpectationResult extends PassingResult {

	/**
	 * @var null|string
	 */
	protected $actual;

	public function __construct( Validator $validator, $message = null, $actual = null ) {
		parent::__construct($validator, $message);
		$this->actual    = $actual;
	}

	/**
	 * @return null|string
	 */
	public function getActual() {
		return $this->actual;
	}

}