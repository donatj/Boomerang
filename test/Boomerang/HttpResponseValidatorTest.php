<?php

namespace Tests\Boomerang;

use Boomerang\Exceptions\InvalidArgumentException;
use Boomerang\HttpResponseValidator;
use Boomerang\Interfaces\HttpResponseInterface;
use PHPUnit\Framework\TestCase;

class HttpResponseValidatorTest extends TestCase {

	public function testGetResponse() : void {
		/**
		 * @var HttpResponseInterface|\PHPUnit\Framework\MockObject\MockObject $mock
		 */
		$mock  = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$valid = new HttpResponseValidator($mock);
		$this->assertSame($mock, $valid->getResponse());
	}

	public function testExpectStatus() : void {
		/**
		 * @var HttpResponseInterface|\PHPUnit\Framework\MockObject\MockObject $mock
		 */
		$mock = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mock->method('getStatus')
			->willReturn(200);

		$valid = new HttpResponseValidator($mock);
		$valid->expectStatus(200);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertFalse($lastExpectation->getFail());

		// ==

		$valid->expectStatus(302);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());
	}

	public function testExpectHeader() : void {
		/**
		 * @var HttpResponseInterface|\PHPUnit\Framework\MockObject\MockObject $mock
		 */
		$mock = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mock->method('getHeader')
			->willReturn(['test']);

		$valid = new HttpResponseValidator($mock);
		$valid->expectHeader('test', 'test');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertFalse($lastExpectation->getFail());

		// == Fail

		$valid->expectHeader('test', 'fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());

		// == False

		$mock->method('getHeader')
			->willReturn([]);

		$valid->expectHeader('test', false);
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());
	}

	public function testExpectHeaderContains() : void {
		/**
		 * @var HttpResponseInterface|\PHPUnit\Framework\MockObject\MockObject $mock
		 */
		$mock = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mock->method('getHeader')
			->with('test')
			->willReturn(['thisIsMyTestHeader']);

		$valid = new HttpResponseValidator($mock);
		$valid->expectHeaderContains('test', 'IsMyTest');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertFalse($lastExpectation->getFail());

		// == Fail

		$valid->expectHeaderContains('test', 'fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());

		// == False

		$mock->method('getHeader')
			->willReturn([]);

		$valid->expectHeaderContains('test', 'funky');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());
	}

	public function testExpectHeaderContains_exception() : void {
		$mock = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$valid = new HttpResponseValidator($mock);

		$this->expectException(InvalidArgumentException::class);
		$valid->expectHeaderContains('test', '');
	}

	public function testExpectBody() : void {
		/**
		 * @var HttpResponseInterface|\PHPUnit\Framework\MockObject\MockObject $mock
		 */
		$mock = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mock->method('getBody')
			->willReturn('test');

		$valid = new HttpResponseValidator($mock);
		$valid->expectBody('test');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertFalse($lastExpectation->getFail());

		// == Fail

		$valid->expectBody('fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());

		// == False

		//		$mock->expects($this->any())
		//		->method('getBody')
		//		->will($this->returnValue(false));
		//
		//		$valid->expectBody(false);
		//		$lastExpectation = end($valid->getExpectationResults());
		//
		//		$this->assertSame(true, $lastExpectation->getFail());
	}

	public function testExpectBodyContains() : void {
		/**
		 * @var HttpResponseInterface|\PHPUnit\Framework\MockObject\MockObject $mock
		 */
		$mock = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mock->method('getBody')
			->willReturn('thisIsMyTestHeader');

		$valid = new HttpResponseValidator($mock);
		$valid->expectBodyContains('IsMyTest');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertFalse($lastExpectation->getFail());

		// == Fail

		$valid->expectBodyContains('fail');
		$expResults      = $valid->getExpectationResults();
		$lastExpectation = end($expResults);

		$this->assertTrue($lastExpectation->getFail());

		// == False

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
