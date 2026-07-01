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

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function match( $data ) {
		return $data === null;
	}

	/**
	 * @return string
	 */
	public function getMatchingTypeName() {
		return 'NULL';
	}

}