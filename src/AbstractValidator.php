<?php

namespace Boomerang;

abstract class AbstractValidator implements Interfaces\ResponseValidatorInterface {

	/** @var Interfaces\ExpectationResultInterface[] */
	protected array $expectations = [];

	/**
	 * @return Interfaces\ExpectationResultInterface[]
	 */
	public function getExpectationResults() : array {
		return $this->expectations;
	}

}
