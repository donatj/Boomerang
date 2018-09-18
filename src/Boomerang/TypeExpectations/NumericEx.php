<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectationInterface;

/**
 * Numeric Expectation
 *
 * Defines a placeholder expectation of a "numeric" (int/float/numeric string) with an optional minimum/maximum range.
 *
 * See: [php.net/is_numeric](http://php.net/is_numeric)
 *
 * **Passes**: `numeric string` / `int` / `float`
 *
 * @package Boomerang\TypeExpectations
 */
class NumericEx implements TypeExpectationInterface {

	/** @var float|int|null */
	protected $min;

	/** @var float|int|null */
	protected $max;

	/**
	 * @param float|int|null $min Optional minimum valid value
	 * @param float|int|null $max Optional maximum valid value
	 */
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
