<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

/**
 * String Expectation
 *
 * Define a string matching placeholder expectation
 */
class StringEx implements TypeExpectationInterface {

	protected ?int $minLength;

	protected ?int $maxLength;

	/**
	 * @param int|null $minLength Optional minimum length in bytes of a valid value
	 * @param int|null $maxLength Optional maximum length in bytes of a valid value
	 */
	public function __construct( ?int $minLength = null, ?int $maxLength = null ) {
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
	}

	public function match( $data ) : bool {
		return is_string($data)
			   && $this->rangeValidation($data);
	}

	protected function rangeValidation( $data ) : bool {
		$len = strlen($data);

		return ($len >= $this->minLength || $this->minLength === null)
			   && ($len <= $this->maxLength || $this->maxLength === null);
	}

	public function getMatchingTypeName() : string {
		if($this->minLength === null && $this->maxLength === null) {
			return 'string';
		}

		return sprintf('string{%s,%s}', strval((int)$this->minLength), strval($this->maxLength ?? '∞'));
	}

}
