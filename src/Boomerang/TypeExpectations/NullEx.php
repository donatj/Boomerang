<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

/**
 * Null Expectation
 *
 * Defines a placeholder expectation of a NULL value.
 *
 * **Passes**: `null`
 */
class NullEx implements TypeExpectationInterface {

	public function match( $data ) {
		return $data === null;
	}

	public function getMatchingTypeName() {
		return 'NULL';
	}

}
