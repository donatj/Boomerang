<?php

namespace Boomerang\Test;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ResponseInterface;
use Boomerang\JSONValidator;
use PHPUnit\Framework\TestCase;

class JSONValidatorTest extends TestCase {

	public function testConstruct() {

		$mock = $this->_getMockResponse('This is not valid JSON');

		$json    = new JSONValidator($mock);
		$results = $json->getExpectationResults();
		$this->assertCount(1, $results);
		$this->assertInstanceOf('\\Boomerang\\ExpectationResults\\FailingResult', reset($results));

		$mock = $this->_getMockResponse( '[1,2,3]' );

		$json    = new JSONValidator($mock);
		$results = $json->getExpectationResults();
		$this->assertCount(1, $results);
		$this->assertTrue(reset($results) instanceof PassingResult);
	}

	/**
	 * @param $data
	 * @return \PHPUnit\Framework\MockObject\MockObject|ResponseInterface
	 */
	private function _getMockResponse( $data ) {
		$mock = $this->getMockBuilder(ResponseInterface::class)->getMock();
		$mock->expects($this->any())
			->method('getBody')
			->willReturn($data);

		return $mock;
	}

}
