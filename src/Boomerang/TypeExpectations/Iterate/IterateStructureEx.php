<?php

namespace Boomerang\TypeExpectations\Iterate;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\TypeExpectations\StructureEx;

/**
 * Iterating Structure (object/array) Expectation
 *
 * Iterates over every element of a iterable structure (object/array), ensuring it iterable, and matching against passed structure expectations.
 *
 * @package Boomerang\TypeExpectations\Iterate
 */
class IterateStructureEx extends StructureEx {

	public function match( $data ) {

		if( !is_array($data) ) {
			$this->addExpectationResults([ new FailingExpectationResult($this->getValidator(), "Data not Iterable", static::getMatchingTypeName(), gettype($data)) ]);

			return false;
		}

		$pass = true;
		foreach( $data as $key => $value ) {
			[$passing, $expectations] = $this->__validate($value, $this->structure, array_merge($this->path, [ $key ]));
			$pass = $pass && $passing;

			$this->addExpectationResults($expectations);
		}

		return $pass;
	}

	public function getMatchingTypeName() {
		return 'object|array';
	}

}
