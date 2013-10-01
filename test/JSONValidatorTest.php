<?php

namespace Boomerang\Test;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ResponseInterface;
use Boomerang\JSONValidator;

class JSONValidatorTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$mock = $this->_getMockResponse('This is not valid JSON');

		$json    = new JSONValidator($mock);
		$results = $json->getExpectationResults();
		$this->assertCount(1, $results);
		$this->assertTrue(reset($results) instanceof FailingResult);


		$mock = $this->_getMockResponse( '[1,2,3]' );

		$json    = new JSONValidator($mock);
		$results = $json->getExpectationResults();
		$this->assertCount(1, $results);
		$this->assertTrue(reset($results) instanceof PassingResult);
	}

	/**
	 * @param $data
	 * @return \PHPUnit_Framework_MockObject_MockObject|ResponseInterface
	 */
	private function _getMockResponse( $data ) {
		$mock = $this->getMock('Boomerang\\Interfaces\\ResponseInterface');
		$mock->expects($this->any())
			->method('getBody')
			->will($this->returnValue( $data ));

		return $mock;
	}

}