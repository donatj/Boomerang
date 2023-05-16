<?php

namespace Boomerang\ExpectationResults;

class FailingResult extends AbstractResult {

	/**
	 * @return bool
	 */
	public function getFail() {
		return true;
	}

}
