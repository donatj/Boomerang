<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class PassingResultTest extends TestCase {

	function testGetFail() {
		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();
		$result        = new PassingResult($mockValidator);
		$this->assertFalse($result->getFail());
	}

}