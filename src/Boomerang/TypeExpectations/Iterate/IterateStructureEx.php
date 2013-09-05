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
			$pass = $pass && $this->__validate($value, $this->structure, array_merge( $this->path, array($key) ));
		}

		return $pass;
	}

}