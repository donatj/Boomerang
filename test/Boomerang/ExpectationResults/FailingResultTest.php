<?php

namespace Tests\Boomerang\ExpectationResults;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\Interfaces\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class FailingResultTest extends TestCase {

	public function testGetFail() : void {
		/**
		 * @var ValidatorInterface $mockValidator
		 */
		$mockValidator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
		$result        = new FailingResult($mockValidator);
		$this->assertTrue($result->getFail());
	}

}
