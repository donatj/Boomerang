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
 */
class AnyEx extends AllEx {

	public function match( $data ) : bool {

		// @todo: __validate should do something other than throw the exceptions into an array, because this can't currently work

		$allExpectations = [];

		foreach( $this->structures as $struct ) {
			[$pass, $expectationResults] = $this->validate($data, $struct);

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

			$allExpectations = array_merge($expectationResults, $allExpectations);
		}

		$this->addExpectationResults($allExpectations);

		return false;
	}

	public function getMatchingTypeName() : string {
		return 'Any (||) Matcher';
	}

}
