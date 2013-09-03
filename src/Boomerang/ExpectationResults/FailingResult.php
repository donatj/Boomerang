<?php

namespace Boomerang\ExpectationResult;

use Boomerang\ExpectationResults\MessageResult;

class FailingResult extends MessageResult {

	/**
	 * @return bool
	 */
	public function getFail() {
		return true;
	}


}