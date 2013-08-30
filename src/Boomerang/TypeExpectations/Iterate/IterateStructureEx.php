<?php

namespace Boomerang\TypeExpectations\Iterate;

use Boomerang\Interfaces\TypeExpectation;
use Boomerang\Validation\HierarchyValidation;

/**
 * @todo Merge this and HierarchyValidation
 */

class IterateStructureEx implements TypeExpectation {

	protected $structure;

	public function __construct( $structure ) {
		$this->structure = $structure;
	}

	public function match( $data ) {
		$pass = true;
		foreach( $data as $key => $value ) {
			$hv   = new HierarchyValidation($value, $this->structure);
			$pass = $pass && $hv->validate();
		}

		return $pass;
	}

}