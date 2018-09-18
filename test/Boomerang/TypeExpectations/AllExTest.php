<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\AllEx;
use PHPUnit\Framework\TestCase;

class AllExTest extends TestCase {

	public function testMatch() {

		$mockPass = $this->_getTypeExpectationInterface(true);
		$mockFail = $this->_getTypeExpectationInterface(false);

		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMockBuilder(ValidatorInterface::class)->getMock();

		$x = new AllEx($mockPass);
		$x->setValidator($mockValidator);

		$this->assertEquals(true, $x->match(true));

		$x = new AllEx($mockFail);
		$x->setValidator($mockValidator);

		$this->assertEquals(false, $x->match(true));

		$x = new AllEx($mockPass, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertEquals(true, $x->match(true));

		$x = new AllEx($mockFail, $mockFail);
		$x->setValidator($mockValidator);

		$this->assertEquals(false, $x->match(true));

		$x = new AllEx($mockPass, $mockPass, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertEquals(true, $x->match(true));

		$x = new AllEx($mockPass, $mockFail, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertEquals(false, $x->match(true));

		$x = new AllEx($mockPass, function () { return true; }, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertEquals(true, $x->match(true));

		$x = new AllEx($mockPass, function () { return false; }, $mockPass);
		$x->setValidator($mockValidator);

		$this->assertEquals(false, $x->match(true));
	}

	/**
	 * @param bool $bool
	 * @return \PHPUnit\Framework\MockObject\MockObject|TypeExpectationInterface
	 */
	private function _getTypeExpectationInterface( $bool ) {
		$mock = $this->getMockBuilder(TypeExpectationInterface::class)->getMock();
		$mock->expects($this->any())
			->method('match')
			->willReturn($bool);

		return $mock;
	}

}