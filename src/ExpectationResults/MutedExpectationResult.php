<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ExpectationResultInterface;

class MutedExpectationResult extends AbstractResult {

	private ExpectationResultInterface $expectationResult;

	public function __construct( ExpectationResultInterface $expectationResult ) {
		$this->expectationResult = $expectationResult;

		parent::__construct($expectationResult->getValidator(), $expectationResult->getMessage());
	}

	public function getExpectationResult() : ExpectationResultInterface {
		return $this->expectationResult;
	}

}
