<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectation;

class StringEx implements TypeExpectation {

	public function match( $data ) {
		return is_string($data);
	}

	public function getMatchingTypeName() {
		return 'string';
	}

}