<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\AnyEx;
use PHPUnit\Framework\TestCase;

class AnyExTest extends TestCase {

	public function testMatch() : void {

		$mockPass = $this->_getTypeExpectationInterface(true);
		$mockFail = $this->_getTypeExpectationInterface(false);

		$x = new AnyEx($mockPass);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertTrue($x->match(true));

		$x = new AnyEx($mockFail);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertFalse($x->match(true));

		$x = new AnyEx($mockPass, $mockPass);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertTrue($x->match(true));

		$x = new AnyEx($mockFail, $mockFail);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertFalse($x->match(true));

		$x = new AnyEx($mockPass, $mockPass, $mockPass);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertTrue($x->match(true));

		$x = new AnyEx($mockPass, $mockFail, $mockPass);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertTrue($x->match(true));

		$x = new AnyEx($mockPass, function () { return true; }, $mockPass);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertTrue($x->match(true));

		$x = new AnyEx($mockPass, function () { return false; }, $mockPass);
		$x->setValidator($this->getMockBuilder(ValidatorInterface::class)->getMock());

		$this->assertTrue($x->match(true));
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
