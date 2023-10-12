<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ValidatorInterface;

class PassingExpectationResult extends PassingResult {

	protected $actual;

	/**
	 * @param mixed|null $actual
	 */
	public function __construct( ValidatorInterface $validator, ?string $message = null, $actual = null ) {
		parent::__construct($validator, $message);

		$this->actual = $actual;
	}

	public function getActual() {
		return $this->actual;
	}

}
