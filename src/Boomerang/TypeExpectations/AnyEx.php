<?php

namespace Boomerang\TypeExpectations;

class AnyEx extends AllEx {

	function match($data) {
		foreach($this->structures as $struct) {
			if( $this->__validate($data, $struct) ) {
				return true;
			}
		}

		return false;
	}

}