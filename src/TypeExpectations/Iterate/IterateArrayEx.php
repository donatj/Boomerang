<?php

namespace Boomerang\TypeExpectations\Iterate;

use Boomerang\ExpectationResults\FailingExpectationResult;

/**
 * Iterating Array Expectation
 *
 * Iterates over every element of an array, ensuring it is an array, and matching against passed structure expectations.
 */
class IterateArrayEx extends IterateStructureEx {

	public function match( $data ) : bool {
		$pass = parent::match($data);

		if( $pass && !static::validType($data) ) {
			$pathname = $this->makePathName($this->path);
			$this->addExpectationResults([ new FailingExpectationResult($this->getValidator(), "Unexpected structure type\n{$pathname}", $this->getMatchingTypeName()) ]);
			$pass = false;
		}

		return $pass;
	}

	/**
	 * Static so its overridable with static::
	 */
	protected static function validType( $data ) : bool {
		return !self::isAssoc($data);
	}

	protected static function isAssoc( $arr ) : bool {
		return is_array($arr) && (array_keys($arr) !== range(0, count($arr) - 1));
	}

	public function getMatchingTypeName() : string {
		return 'array';
	}

}
