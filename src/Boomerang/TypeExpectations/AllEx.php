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
 *
 * @package Boomerang\TypeExpectations
 */
class AllEx extends StructureEx {

	/**
	 * @var TypeExpectationInterface[]
	 */
	protected $structures;

	/**
	 * @param TypeExpectationInterface|callable|mixed ...$structures One or more structure definitions to match
	 */
	public function __construct( ...$structures ) {
		$this->structures = $structures;
	}

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function match( $data ) {
		$pass = true;

		foreach( $this->structures as $struct ) {
			[$passing, $expectations] = $this->__validate($data, $struct);
			$this->addExpectationResults($expectations);
			$pass = $pass && $passing;
		}

		return $pass;
	}

	/**
	 * @return string
	 */
	public function getMatchingTypeName() {
		return 'All (&&) Matcher';
	}
}