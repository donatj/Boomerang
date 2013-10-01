<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

class StringEx implements TypeExpectationInterface {

	public function match( $data ) {
		return is_string($data);
	}

	public function getMatchingTypeName() {
		return 'string';
	}

}