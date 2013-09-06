<?php

namespace Boomerang\TypeExpectations;

class IntEx extends NumericEx {

	public function match( $data ) {
		return !is_string($data)
			   && is_numeric($data)
			   && intval($data) == $data
			   && $this->rangeValidation($data);
	}

	public function getMatchingTypeName() {
		return 'int';
	}

}