<?php

namespace Boomerang\TypeExpectations;

/**
 * Integer Expectation
 *
 * Defines a placeholder expectation of an integer with an optional minimum/maximum range.
 *
 * **Passes**: int
 * **Fails**: float/numeric string
 */
class IntEx extends NumberEx {

	public function match( $data ) {
		return intval($data) == $data
			   && parent::match($data);
	}

	public function getMatchingTypeName() {
		return 'int';
	}

}
