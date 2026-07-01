<?php

namespace Boomerang\TypeExpectations;

/**
 * Number Expectation
 *
 * Defines a placeholder expectation of a "number" (int/float) with an optional minimum/maximum range.
 *
 * **Passes**: `int` / `float`
 * **Fails**: `numeric string`
 *
 * @package Boomerang\TypeExpectations
 */
class NumberEx extends NumericEx {

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function match( $data ) {
		return !is_string($data)
			   && parent::match($data);
	}

	/**
	 * @return string
	 */
	public function getMatchingTypeName() {
		return 'int|float';
	}

}