<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\AllEx;
use PHPUnit\Framework\TestCase;

class AllExTest extends TestCase {

	public function testMatch() : void {

		$mockPass = $this->_getTypeExpectationInterface(true);
		$mockFail = $this->_getTypeExpectationInterface(false);

		/**
		 * @var ValidatorInterface $mockValidator
		 */
		$mockValidator = $this->getMockBuilder(ValidatorInterface::class)->getMock();

		$x = new AllEx($mockPass);
		$x->setValidator($mockValidator);

		$this->assertTrue($x->match(true));

		$x = new AllEx($mockFail);
		$x->setValidator($mockValidator);

		$this->assertFalse($x->match(true));

		$x = new AllEx($mockPass, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertTrue($x->match(true));

		$x = new AllEx($mockFail, $mockFail);
		$x->setValidator($mockValidator);

		$this->assertFalse($x->match(true));

		$x = new AllEx($mockPass, $mockPass, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertTrue($x->match(true));

		$x = new AllEx($mockPass, $mockFail, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertFalse($x->match(true));

		$x = new AllEx($mockPass, function () { return true; }, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertTrue($x->match(true));

		$x = new AllEx($mockPass, function () { return false; }, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertFalse($x->match(true));
	}

	/**
	 * @return \PHPUnit\Framework\MockObject\MockObject|TypeExpectationInterface
	 */
	private function _getTypeExpectationInterface( bool $bool ) {
		$mock = $this->getMockBuilder(TypeExpectationInterface::class)->getMock();
		$mock->method('match')
			->willReturn($bool);

		return $mock;
	}

}
