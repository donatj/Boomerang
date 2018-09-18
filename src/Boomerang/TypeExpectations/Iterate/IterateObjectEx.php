<?php

namespace Boomerang\TypeExpectations\Iterate;

/**
 * Iterating Object Expectation
 *
 * Iterates over every element of an object, ensuring it is an object, and matching against passed structure expectations.
 *
 * @package Boomerang\TypeExpectations\Iterate
 */
class IterateObjectEx extends IterateArrayEx {

	/**
	 * Static so its can be overridden with static::
	 *
	 * @return bool
	 */
	protected static function validType( $data ) {
		return self::isAssoc($data);
	}

	public function getMatchingTypeName() {
		return 'object';
	}

}
