<?php

namespace Boomerang\TypeExpectations;

class IntEx extends NumberEx {

	public function match( $data ) {
		if( !is_string($data) && is_numeric($data) && intval($data) == $data
			&& $this->rangeValidation()
		) {
			return true;
		}

		return false;
	}

}