<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

class AllEx extends StructureEx {

	/**
	 * @var TypeExpectationInterface[]
	 */
	protected $structures;

	function __construct( /* ... */ ) {
		$this->structures = func_get_args();
	}

	function match($data) {
		$pass = true;

		foreach($this->structures as $struct) {
			list($passing, $expectations) = $this->__validate($data, $struct);
			$this->addExpectations($expectations);
			$pass = $pass && $passing;
		}

		return $pass;
	}

	public function getMatchingTypeName() {
		return 'All (&&) Matcher';
	}
}