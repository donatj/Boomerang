<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\StructureEx;
use PHPUnit\Framework\TestCase;

class StructureExTest extends TestCase {

	public function testArrayTypedClosureRejectsNonArrayWithoutDeprecation() {
		/** @var ValidatorInterface $validator */
		$validator = $this->getMockBuilder('Boomerang\\Interfaces\\ValidatorInterface')->getMock();
		$structure = new StructureEx(function ( array $data ) { return true; });
		$structure->setValidator($validator);

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

}
