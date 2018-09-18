<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class PassingResultTest extends TestCase {

	public function testGetFail() {
		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
		$result        = new PassingResult($mockValidator);
		$this->assertFalse($result->getFail());
	}

}
