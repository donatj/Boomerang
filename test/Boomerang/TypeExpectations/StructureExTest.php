<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\StructureEx;
use PHPUnit\Framework\TestCase;

class StructureExTest extends TestCase {

	public function testClosureReturningTruePasses() {
		$structure = $this->createStructure(function ( $data ) : bool { return true; });

		$this->assertTrue($structure->match('test'));
	}

	public function testClosureReturningFalseFails() {
		$structure = $this->createStructure(function ( $data ) : bool { return false; });

		$this->assertFalse($structure->match('test'));
		$this->assertInstanceOf(FailingExpectationResult::class, $this->getOnlyExpectation($structure));
	}

	public function testClosureExceptionBecomesAFailingExpectation() {
		$structure = $this->createStructure(function ( $data ) : bool {
			throw new \RuntimeException('Something went wrong');
		});

		$this->assertFalse($structure->match('test'));

		$expectation = $this->getOnlyExpectation($structure);
		$this->assertInstanceOf(FailingExpectationResult::class, $expectation);
		$this->assertStringContainsString('RuntimeException', (string)$expectation->getMessage());
		$this->assertSame('(no exception)', $expectation->getExpected());
		$this->assertSame('Something went wrong', $expectation->getActual());
	}

	public function testClosureTypeErrorBecomesAFailingExpectationWithItsPath() {
		$structure = new StructureEx([
			'nested' => function ( \stdClass $data ) : bool { return true; },
		]);
		$structure->setValidator($this->createValidator());

		$this->assertFalse($structure->match([ 'nested' => 'not an object' ]));

		$expectation = $this->getOnlyExpectation($structure);
		$this->assertInstanceOf(FailingExpectationResult::class, $expectation);
		$this->assertStringContainsString('TypeError', (string)$expectation->getMessage());
		$this->assertStringContainsString('.nested', (string)$expectation->getMessage());
	}

	public function testArrayTypedClosureRejectsNonArrayWithoutDeprecation() {
		$structure = $this->createStructure(function ( array $data ) { return true; });

		set_error_handler(function ( $severity, $message ) {
			if( $severity === E_DEPRECATED ) {
				throw new \ErrorException($message, 0, $severity);
			}

			return false;
		});

		try {
			$this->assertFalse($structure->match('not an array'));
		} finally {
			restore_error_handler();
		}
	}

	public function testInvalidExpectationResultsAreRejected() {
		$structure = new class(null) extends StructureEx {

			public function addInvalidExpectationResult( $expectation ) {
				$this->addExpectationResults([ $expectation ]);
			}

		};

		$this->expectException(\InvalidArgumentException::class);
		$structure->addInvalidExpectationResult(new \stdClass);
	}

	private function createStructure( \Closure $validation ) : StructureEx {
		$structure = new StructureEx($validation);
		$structure->setValidator($this->createValidator());

		return $structure;
	}

	private function createValidator() : ValidatorInterface {
		return $this->createMock(ValidatorInterface::class);
	}

	private function getOnlyExpectation( StructureEx $structure ) : FailingExpectationResult {
		$expectations = array_values($structure->getExpectationResults());

		$this->assertCount(1, $expectations);
		$this->assertInstanceOf(FailingExpectationResult::class, $expectations[0]);

		return $expectations[0];
	}

}
