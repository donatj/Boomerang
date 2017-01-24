<?php

namespace Boomerang\Test;

use Boomerang\HttpResponseValidator;
use Boomerang\Interfaces\HttpResponseInterface;

class HttpResponseValidatorTest extends \PHPUnit_Framework_TestCase {


	public function testGetResponse() {
		/**
		 * @var $mock HttpResponseInterface|\PHPUnit_Framework_MockObject_MockObject
		 */
		$mock  = $this->getMock('Boomerang\\Interfaces\\HttpResponseInterface');
		$valid = new HttpResponseValidator($mock);
		$this->assertSame($mock, $valid->getResponse());
	}

	public function testExpectStatus() {
		/**
		 * @var $mock HttpResponseInterface|\PHPUnit_Framework_MockObject_MockObject
		 */
		$mock = $this->getMock('Boomerang\\Interfaces\\HttpResponseInterface');
		$mock->expects($this->any())
			->method('getStatus')
			->will($this->returnValue(200));

		$valid = new HttpResponseValidator($mock);
		$valid->expectStatus(200);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(false, $lastExpectation->getFail());

		# ==

		$valid->expectStatus(302);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());
	}

	public function testExpectHeader() {
		/**
		 * @var $mock HttpResponseInterface|\PHPUnit_Framework_MockObject_MockObject
		 */
		$mock = $this->getMock('Boomerang\\Interfaces\\HttpResponseInterface');
		$mock->expects($this->any())
			->method('getHeader')
			->will($this->returnValue('test'));

		$valid = new HttpResponseValidator($mock);
		$valid->expectHeader('test', 'test');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(false, $lastExpectation->getFail());

		# == Fail

		$valid->expectHeader('test', 'fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());

		# == False

		$mock->expects($this->any())
			->method('getHeader')
			->will($this->returnValue(false));

		$valid->expectHeader('test', false);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());
	}

	public function testExpectHeaderContains() {
		/**
		 * @var $mock HttpResponseInterface|\PHPUnit_Framework_MockObject_MockObject
		 */
		$mock = $this->getMock('Boomerang\\Interfaces\\HttpResponseInterface');
		$mock->expects($this->any())
			->method('getHeader')
			->will($this->returnValue('thisIsMyTestHeader'));

		$valid = new HttpResponseValidator($mock);
		$valid->expectHeaderContains('test', 'IsMyTest');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(false, $lastExpectation->getFail());

		# == Fail

		$valid->expectHeaderContains('test', 'fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());

		# == False

		$mock->expects($this->any())
			->method('getHeader')
			->will($this->returnValue(false));

		$valid->expectHeaderContains('test', false);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());
	}

	public function testExpectBody() {
		/**
		 * @var $mock HttpResponseInterface|\PHPUnit_Framework_MockObject_MockObject
		 */
		$mock = $this->getMock('Boomerang\\Interfaces\\HttpResponseInterface');
		$mock->expects($this->any())
			->method('getBody')
			->will($this->returnValue('test'));

		$valid = new HttpResponseValidator($mock);
		$valid->expectBody('test');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(false, $lastExpectation->getFail());

		# == Fail

		$valid->expectBody('fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());

		# == False

		//		$mock->expects($this->any())
		//		->method('getBody')
		//		->will($this->returnValue(false));
		//
		//		$valid->expectBody(false);
		//		$lastExpectation = end($valid->getExpectationResults());
		//
		//		$this->assertSame(true, $lastExpectation->getFail());
	}

	public function testExpectBodyContains() {
		/**
		 * @var $mock HttpResponseInterface|\PHPUnit_Framework_MockObject_MockObject
		 */
		$mock = $this->getMock('Boomerang\\Interfaces\\HttpResponseInterface');
		$mock->expects($this->any())
			->method('getBody')
			->will($this->returnValue('thisIsMyTestHeader'));

		$valid = new HttpResponseValidator($mock);
		$valid->expectBodyContains('IsMyTest');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(false, $lastExpectation->getFail());

		# == Fail

		$valid->expectBodyContains('fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertSame(true, $lastExpectation->getFail());

		# == False

		//		$mock->expects($this->any())
		//		->method('getBody')
		//		->will($this->returnValue(false));
		//
		//		$valid->expectBodyContains('test', false);
		//		$lastExpectation = end($valid->getExpectationResults());
		//
		//		$this->assertSame(true, $lastExpectation->getFail());
	}

	//	public function testGetExpectationResults() {
	//
	//	}
}