<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\TypeExpectations\AnyEx;
use PHPUnit\Framework\TestCase;

class AnyExTest extends TestCase {

	public function testMatch() {

		$mockPass = $this->_getTypeExpectationInterface(true);
		$mockFail = $this->_getTypeExpectationInterface(false);

		$x = new AnyEx($mockPass);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(true, $x->match(true));

		$x = new AnyEx($mockFail);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(false, $x->match(true));

		$x = new AnyEx($mockPass, $mockPass);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(true, $x->match(true));

		$x = new AnyEx($mockFail, $mockFail);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(false, $x->match(true));

		$x = new AnyEx($mockPass, $mockPass, $mockPass);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(true, $x->match(true));

		$x = new AnyEx($mockPass, $mockFail, $mockPass);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(true, $x->match(true));

		$x = new AnyEx($mockPass, function () { return true; }, $mockPass);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(true, $x->match(true));

		$x = new AnyEx($mockPass, function () { return false; }, $mockPass);
		$x->setValidator($this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock());

		$this->assertEquals(true, $x->match(true));
	}

	/**
	 * @param bool $bool
	 * @return \PHPUnit\Framework\MockObject\MockObject|TypeExpectationInterface
	 */
	private function _getTypeExpectationInterface( $bool ) {
		$mock = $this->getMockBuilder('Boomerang\\Interfaces\\TypeExpectationInterface')->getMock();
		$mock->method('match')
			->willReturn($bool);

		return $mock;
	}

}
