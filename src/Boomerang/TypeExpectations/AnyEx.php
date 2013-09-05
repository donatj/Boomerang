<?php

namespace Boomerang\TypeExpectations;

class AnyEx extends AllEx {

	function match($data) {

		// @todo: __validate should do something other than throw the exceptions into an array, because this can't currently work

		foreach($this->structures as $struct) {
			if( $this->__validate($data, $struct) ) {
				return true;
			}
		}

		return false;
	}

}