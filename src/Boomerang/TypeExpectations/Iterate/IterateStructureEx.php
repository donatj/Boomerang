<?php

namespace Boomerang\TypeExpectations\Iterate;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\TypeExpectations\StructureEx;


class IterateStructureEx extends StructureEx {

	public function match( $data ) {

		if( !is_array($data) ) {
			$this->addExpectations(array( new FailingExpectationResult($this->getValidator(), "Data not Iterable", static::getMatchingTypeName(), gettype($data)) ));
			return false;
		}

		$pass = true;
		foreach( $data as $key => $value ) {
			list($passing, $expectations) = $this->__validate($value, $this->structure, array_merge($this->path, array( $key )));
			$pass = $pass && $passing;

			$this->addExpectations($expectations);
		}

		return $pass;
	}

	public function getMatchingTypeName() {
		return 'object|array';
	}

}