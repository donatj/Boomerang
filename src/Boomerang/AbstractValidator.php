<?php

namespace Boomerang;

abstract class AbstractValidator implements Interfaces\ResponseValidatorInterface {

	/**
	 * @var Interfaces\ExpectationResultInterface[]
	 */
	protected $expectations = [];

	/**
	 * @return Interfaces\ExpectationResultInterface[]
	 */
	public function getExpectationResults() {
		return $this->expectations;
	}

}
