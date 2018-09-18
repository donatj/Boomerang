<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\Interfaces\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class FailingResultTest extends TestCase {

	public function testGetFail() {
		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
		$result        = new FailingResult($mockValidator);
		$this->assertTrue($result->getFail());
	}

}
