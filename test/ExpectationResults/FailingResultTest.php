<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\Interfaces\ValidatorInterface;

class FailingResultTest extends \PHPUnit_Framework_TestCase {

	function testGetFail() {
		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMock('Boomerang\\Interfaces\\ValidatorInterface');
		$result        = new FailingResult($mockValidator);
		$this->assertTrue($result->getFail());
	}

}