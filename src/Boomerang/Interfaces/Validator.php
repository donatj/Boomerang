<?php

namespace Boomerang\Interfaces;

interface Validator {

	/**
	 * @return ExpectationResult[]
	 */
	public function getExpectationResults();

}