<?php

namespace Tests\Boomerang\ExpectationResults;

use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class PassingResultTest extends TestCase {

	public function testGetFail() : void {
		/**
		 * @var ValidatorInterface $mockValidator
		 */
		$mockValidator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
		$result        = new PassingResult($mockValidator);
		$this->assertFalse($result->getFail());
	}

}
