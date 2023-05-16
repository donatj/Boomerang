<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\FailingResult;
use PHPUnit\Framework\TestCase;

class FailingResultTest extends TestCase {

	public function testGetFail() {
		/**
		 * @var ValidatorInterface $mockValidator
		 */
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();
		$result        = new FailingResult($mockValidator);
		$this->assertTrue($result->getFail());
	}

}
