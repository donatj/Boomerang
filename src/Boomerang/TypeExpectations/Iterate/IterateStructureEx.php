<?php

namespace Boomerang\TypeExpectations\Iterate;

use Boomerang\TypeExpectations\StructureEx;


/**
 * @todo Merge this and HierarchyValidation
 */

class IterateStructureEx extends StructureEx {

	public function match( $data ) {
		$pass = true;
		foreach( $data as $key => $value ) {
			list($passing, $expectations) = $this->__validate($value, $this->structure, array_merge( $this->path, array($key) ));
			$pass = $pass && $passing;

			$this->addExpectations($expectations);
		}

		return $pass;
	}

	public function getMatchingTypeName() {
		return 'object|array iterate';
	}

}