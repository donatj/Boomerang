<?php

namespace Boomerang\TypeExpectations;

class AnyEx extends AllEx {

	function match( $data ) {

		// @todo: __validate should do something other than throw the exceptions into an array, because this can't currently work

		$all_expectations = array();

		foreach( $this->structures as $struct ) {
			list($pass, $expectations) = $this->__validate($data, $struct);

			if( $pass ) {
				//we only add the passing ones because having failing ones denotes failure
				$this->addExpectations($expectations);

				return true;
			}

			$all_expectations = array_merge( $expectations, $all_expectations );
		}

		$this->addExpectations($all_expectations);
		return false;
	}

	public function getMatchingTypeName() {
		return 'Any (||) Matcher';
	}

}