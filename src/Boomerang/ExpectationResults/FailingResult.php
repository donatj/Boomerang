<?php

namespace Boomerang\ExpectationResults;

class FailingResult extends AbstractResult {

	public function getFail() : bool {
		return true;
	}

}
