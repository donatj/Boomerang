<?php

namespace Boomerang\ExpectationResult;

use Boomerang\ExpectationResults\BaseResult;

class FailingResult extends BaseResult {

	/**
	 * @return bool
	 */
	public function getFail() {
		return true;
	}


}