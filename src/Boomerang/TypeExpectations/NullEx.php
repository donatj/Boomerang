<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

/**
 * Null Expectation
 *
 * Defines a placeholder expectation of a NULL value.
 *
 * **Passes**: `null`
 *
 * @package Boomerang\TypeExpectations
 */
class NullEx implements TypeExpectationInterface {

	public function match( $data ) {
		return is_null($data);
	}

	public function getMatchingTypeName() {
		return 'NULL';
	}

}
