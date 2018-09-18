<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ValidatorInterface;

class PassingExpectationResult extends PassingResult {

	/**
	 * @var mixed
	 */
	protected $actual;

	/**
	 * @param string|null        $message
	 */
	public function __construct( ValidatorInterface $validator, $message = null, $actual = null ) {
		parent::__construct($validator, $message);
		$this->actual = $actual;
	}

	public function getActual() {
		return $this->actual;
	}

}
