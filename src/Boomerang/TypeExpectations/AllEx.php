<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

/**
 * All Expectation
 *
 * Defines a requirement to match **all** structure definitions expectations.
 *
 * Example:
 *
 *     new AllEx(
 *         array(1,2,3),
 *         function($data) { return count($data) == 3; }
 *     );
 */
class AllEx extends StructureEx {

	/** @var TypeExpectationInterface[] */
	protected array $structures;

	public function __construct( $structure ) {
		$this->structures = func_get_args();
	}

	public function match( $data ) : bool {
		$pass = true;

		foreach( $this->structures as $struct ) {
			[$passing, $expectations] = $this->__validate($data, $struct);
			$this->addExpectationResults($expectations);
			$pass = $pass && $passing;
		}

		return $pass;
	}

	public function getMatchingTypeName() : string {
		return 'All (&&) Matcher';
	}

}
