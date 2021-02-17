<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\Interfaces\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class FailingResultTest extends TestCase {

	function testGetFail() {
		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();
		$result        = new FailingResult($mockValidator);
		$this->assertTrue($result->getFail());
	}

}