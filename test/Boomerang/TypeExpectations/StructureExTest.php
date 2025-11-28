<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\StructureEx;
use PHPUnit\Framework\TestCase;

class StructureExTest extends TestCase {

	/**
	 * Test that a closure returning true passes
	 */
	public function testClosureReturningTruePasses() {
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();

		$structure = new StructureEx(function($data) {
			return true;
		});
		$structure->setValidator($mockValidator);

		$this->assertTrue($structure->match('test'));
	}

	/**
	 * Test that a closure returning false fails
	 */
	public function testClosureReturningFalseFails() {
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();

		$structure = new StructureEx(function($data) {
			return false;
		});
		$structure->setValidator($mockValidator);

		$this->assertFalse($structure->match('test'));
	}

	/**
	 * Test that a closure throwing an exception is handled gracefully
	 */
	public function testClosureThrowingExceptionIsHandled() {
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();

		$structure = new StructureEx(function($data) {
			throw new \RuntimeException('Test exception message');
		});
		$structure->setValidator($mockValidator);

		// Should not throw, should return false
		$result = $structure->match('test');
		$this->assertFalse($result);

		// Should have a failing expectation result
		$expectations = $structure->getExpectationResults();
		$this->assertCount(1, $expectations);

		$expectation = reset($expectations);
		$this->assertInstanceOf(FailingExpectationResult::class, $expectation);
		$this->assertStringContainsString('RuntimeException', $expectation->getMessage());
		$this->assertEquals('Test exception message', $expectation->getActual());
	}

	/**
	 * Test that a closure throwing an InvalidArgumentException is handled gracefully
	 */
	public function testClosureThrowingInvalidArgumentExceptionIsHandled() {
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();

		$structure = new StructureEx(function($data) {
			throw new \InvalidArgumentException('Invalid argument test');
		});
		$structure->setValidator($mockValidator);

		// Should not throw, should return false
		$result = $structure->match('test');
		$this->assertFalse($result);

		// Should have a failing expectation result
		$expectations = $structure->getExpectationResults();
		$this->assertCount(1, $expectations);

		$expectation = reset($expectations);
		$this->assertInstanceOf(FailingExpectationResult::class, $expectation);
		$this->assertStringContainsString('InvalidArgumentException', $expectation->getMessage());
		$this->assertEquals('Invalid argument test', $expectation->getActual());
	}

	/**
	 * Test that the error message includes the path information
	 */
	public function testClosureExceptionIncludesPath() {
		$mockValidator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();

		$structure = new StructureEx(function($data) {
			throw new \LogicException('Path test message');
		});
		$structure->setValidator($mockValidator);

		$result = $structure->match('test');
		$this->assertFalse($result);

		$expectations = $structure->getExpectationResults();
		$expectation = reset($expectations);
		
		// The message should contain "threw" and the exception class name
		$this->assertStringContainsString('threw', $expectation->getMessage());
		$this->assertStringContainsString('LogicException', $expectation->getMessage());
	}

}
