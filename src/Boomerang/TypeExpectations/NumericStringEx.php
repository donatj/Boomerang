<?php

namespace Boomerang\TypeExpectations;

/**
 * Numeric String Expectation
 *
 * Defines a placeholder expectation of a "numeric string" with an optional minimum/maximum range.
 *
 * **Passes**: `numeric string` eg: "1.2"
 * **Fails**: `int` / `float`
 *
 * @package Boomerang\TypeExpectations
 */
class NumericStringEx extends NumericEx {

	public function match( $data ) {
		return is_string($data)
			   && parent::match($data);
	}

	public function getMatchingTypeName() {
		return 'numeric string';
	}

}
