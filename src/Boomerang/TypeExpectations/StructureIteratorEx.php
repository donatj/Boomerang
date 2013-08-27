<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectation;
use Boomerang\Validation\HierarchyValidation;

/**
 * @todo Merge this and HierarchyValidation
 */

class StructureIteratorEx implements TypeExpectation {

	private $structure;

	function __construct( $structure ) {
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