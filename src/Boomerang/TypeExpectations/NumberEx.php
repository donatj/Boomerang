<?php

namespace Boomerang\TypeExpectations;

class NumberEx extends NumericEx {

	public function match( $data ) {
		return !is_string($data)
			   && is_numeric($data)
			   && $this->rangeValidation($data);
	}

	public function getMatchingTypeName() {
		return 'int|float';
	}

}