<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

class NullEx implements TypeExpectationInterface {

	public function match( $data ) {
		return is_null($data);
	}

	public function getMatchingTypeName() {
		return 'NULL';
	}

}