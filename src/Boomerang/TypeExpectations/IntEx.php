<?php

namespace Boomerang\TypeExpectations;

/**
 * Integer Expectation
 *
 * Defines a placeholder expectation of an integer with an optional minimum/maximum range.
 *
 * **Passes**: int
 * **Fails**: float/numeric string
 *
 * @package Boomerang\TypeExpectations
 */
class IntEx extends NumberEx {

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function match( $data ) {
		return intval($data) == $data
			   && parent::match($data);
	}

	/**
	 * @return string
	 */
	public function getMatchingTypeName() {
		return 'int';
	}

}