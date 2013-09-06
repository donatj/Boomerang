<?php

namespace Boomerang\TypeExpectations;

class IntEx extends NumberEx {

	public function match( $data ) {
		return intval($data) == $data
			   && parent::match($data);
	}

	public function getMatchingTypeName() {
		return 'int';
	}

}