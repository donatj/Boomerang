<?php

namespace Boomerang\ExpectationResults;

use Boomerang\ExpectationResults\AbstractResult;

class FailingResult extends AbstractResult {

	/**
	 * @return bool
	 */
	public function getFail() {
		return true;
	}


}