<?php

namespace Boomerang\TypeExpectations\Iterate;

class IterateObjectEx extends IterateArrayEx {

	/**
	 * Static so its overridable with static::
	 */
	protected static function validType( $data ) {
		return self::isAssoc($data);
	}

}

