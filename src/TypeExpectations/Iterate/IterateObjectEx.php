<?php

namespace Boomerang\TypeExpectations\Iterate;

/**
 * Iterating Object Expectation
 *
 * Iterates over every element of an object, ensuring it is an object, and matching against passed structure expectations.
 */
class IterateObjectEx extends IterateArrayEx {

	/**
	 * Static so its can be overridden with static::
	 */
	protected static function validType( $data ) : bool {
		return self::isAssoc($data);
	}

	public function getMatchingTypeName() : string {
		return 'object';
	}

}
