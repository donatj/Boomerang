<?php

namespace Boomerang\ExpectationResults\Test;

use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ValidatorInterface;

class PassingResultTest extends \PHPUnit_Framework_TestCase {

	public function testGetFail() {
		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMock('Boomerang\\Interfaces\\ValidatorInterface');
		$result        = new PassingResult($mockValidator);
		$this->assertFalse($result->getFail());
	}

}
