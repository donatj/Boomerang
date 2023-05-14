<?php

namespace Boomerang\Exceptions;

trait CliExceptionTrait {

	/**
	 * @return int
	 */
	abstract public function getCode();

	public function getExitCode() : int {
		$code = $this->getCode();
		if( $code >= 0 && $code <= 255 ) {
			return $code;
		}

		throw new \OutOfRangeException("Invalid CLI Error Code. {$code} outside of 0->255");
	}

}
