<?php

namespace Boomerang\TypeExpectations;

/**
 * Number Expectation
 *
 * Defines a placeholder expectation of a "number" (int/float) with an optional minimum/maximum range.
 *
 * **Passes**: `int` / `float`
 * **Fails**: `numeric string`
 */
class NumberEx extends NumericEx {

	public function match( $data ) : bool {
		return !is_string($data)
			   && parent::match($data);
	}

	public function getMatchingTypeName() : string {
		return 'int|float';
	}

}
