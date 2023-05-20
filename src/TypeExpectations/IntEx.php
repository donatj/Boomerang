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

	public function match( $data ) : bool {
		return (int)$data == $data
			   && parent::match($data);
	}

	public function getMatchingTypeName() : string {
		return 'int';
	}

}
