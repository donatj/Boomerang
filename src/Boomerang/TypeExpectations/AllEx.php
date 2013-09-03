<?php

namespace Boomerang\TypeExpectations;

class AllEx extends StructureEx {

	protected $structures;

	function __construct( /* ... */ ) {
		$this->structures = func_get_args();
	}

	function match($data) {
		$pass = true;

		foreach($this->structures as $struct) {
			$pass = $pass && $this->__validate($data, $struct);
		}

		return $pass;
	}

}