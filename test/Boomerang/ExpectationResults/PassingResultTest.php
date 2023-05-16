<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\PassingResult;
use PHPUnit\Framework\TestCase;

class PassingResultTest extends TestCase {

	public function testGetFail() {
		/**
		 * @var ValidatorInterface $mockValidator
		 */
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();
		$result        = new PassingResult($mockValidator);
		$this->assertFalse($result->getFail());
	}

}
