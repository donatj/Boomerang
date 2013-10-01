<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

class NumericEx implements TypeExpectationInterface {

	protected $min;
	protected $max;

	public function __construct( $min = null, $max = null ) {
		$this->min = $min;
		$this->max = $max;
	}

	public function match( $data ) {
		return is_numeric($data)
			   && $this->rangeValidation($data);
	}

	protected function rangeValidation( $data ) {
		return ($data >= $this->min || $this->min === null)
			   && ($data <= $this->max || $this->max === null);
	}

	public function getMatchingTypeName() {
		return 'int|float|numeric string';
	}

}