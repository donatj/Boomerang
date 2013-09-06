<?php

namespace Boomerang\TypeExpectations;

class NumericStringEx extends NumericEx {

	public function match( $data ) {
		return is_string($data)
			   && parent::match($data);
	}

	public function getMatchingTypeName() {
		return 'numeric string';
	}

}