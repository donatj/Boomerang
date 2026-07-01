<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ExpectationResultInterface;

class MutedExpectationResult extends AbstractResult {

	private ExpectationResultInterface $expecationResult;

	public function __construct( ExpectationResultInterface $expectationResult ) {
		$this->expecationResult = $expectationResult;
		parent::__construct($expectationResult->getValidator(), $expectationResult->getMessage());
	}

	/**
	 * @return \Boomerang\Interfaces\ExpectationResultInterface
	 */
	public function getExpecationResult() {
		return $this->expecationResult;
	}

}