<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\TypeExpectations\AllEx;

class AllExTest extends \PHPUnit_Framework_TestCase {

	public function testMatch() {

		$mockPass = $this->_getTypeExpectationInterface(true);
		$mockFail = $this->_getTypeExpectationInterface(false);


		$x = new AllEx($mockPass);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(true, $x->match(true));


		$x = new AllEx($mockFail);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(false, $x->match(true));


		$x = new AllEx($mockPass, $mockPass);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(true, $x->match(true));


		$x = new AllEx($mockFail, $mockFail);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(false, $x->match(true));


		$x = new AllEx($mockPass, $mockPass, $mockPass);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(true, $x->match(true));


		$x = new AllEx($mockPass, $mockFail, $mockPass);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(false, $x->match(true));


		$x = new AllEx($mockPass, function(){ return true; }, $mockPass);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(true, $x->match(true));


		$x = new AllEx($mockPass, function(){ return false; }, $mockPass);
		$x->setValidator($this->getMock('Boomerang\\Interfaces\\ValidatorInterface'));

		$this->assertEquals(false, $x->match(true));


	}

	/**
	 * @param bool $bool
	 * @return \PHPUnit_Framework_MockObject_MockObject|TypeExpectationInterface
	 */
	private function _getTypeExpectationInterface( $bool ) {
		$mock = $this->getMock('Boomerang\\Interfaces\\TypeExpectationInterface');
		$mock->expects($this->any())
		->method('match')
		->will($this->returnValue($bool));

		return $mock;
	}

}