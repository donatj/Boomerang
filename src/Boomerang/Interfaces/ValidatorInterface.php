<?php

namespace Boomerang\Interfaces;

interface ValidatorInterface {

	/**
	 * @return ExpectationResultInterface[]
	 */
	public function getExpectationResults();

}