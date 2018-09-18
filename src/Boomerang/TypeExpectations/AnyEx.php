<?php

namespace Boomerang\TypeExpectations;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\MutedExpectationResult;
use Boomerang\Interfaces\ExpectationResultInterface;

/**
 * Any Expectation
 *
 * Defines a requirement to match **any** structure definitions expectations.
 *
 * Example:
 *
 *     new AllEx(
 *         array(1,2,3),
 *         function($data) { return count($data) == 4; }
 *     );
 *
 * @package Boomerang\TypeExpectations
 */
class AnyEx extends AllEx {

	public function match( $data ) {

		// @todo: __validate should do something other than throw the exceptions into an array, because this can't currently work

		$all_expectations = [];

		foreach( $this->structures as $struct ) {
			[$pass, $expectationResults] = $this->__validate($data, $struct);

			if( $pass ) {

				$expectationResults = array_map(function ( ExpectationResultInterface $result ) {
					if( $result instanceof FailingResult ) {
						return new MutedExpectationResult($result);
					}

					return $result;
				}, $expectationResults);

				$this->addExpectationResults($expectationResults);

				return true;
			}

			$all_expectations = array_merge($expectationResults, $all_expectations);
		}

		$this->addExpectationResults($all_expectations);

		return false;
	}

	public function getMatchingTypeName() {
		return 'Any (||) Matcher';
	}

}
