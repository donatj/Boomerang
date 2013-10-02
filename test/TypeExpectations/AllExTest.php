<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\AllEx;

class AllExTest extends \PHPUnit_Framework_TestCase {

	public function testMatch() {

		$mockPass = $this->_getTypeExpectationInterface(true);
		$mockFail = $this->_getTypeExpectationInterface(false);

		/**
		 * @var $mockValidator ValidatorInterface
		 */
		$mockValidator = $this->getMock('Boomerang\\Interfaces\\ValidatorInterface');


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