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
			list($passing, $expectations) = $this->__validate($data, $struct);
			$pass = $pass && $passing;
		}

		return $pass;
	}

}