<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectation;

class NullEx implements TypeExpectation {

	public function match( $data ) {
		return is_null($data);
	}

	public function getMatchingTypeName() {
		return 'NULL';
	}

}