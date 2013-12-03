<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

/**
 * String Expectation
 *
 * Define a string matching placeholder expectation
 *
 * @package Boomerang\TypeExpectations
 */
class StringEx implements TypeExpectationInterface {

	public function match( $data ) {
		return is_string($data);
	}

	public function getMatchingTypeName() {
		return 'string';
	}

}