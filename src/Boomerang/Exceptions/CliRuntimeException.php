<?php

namespace Boomerang\Exceptions;

class CliRuntimeException extends \RuntimeException implements CliExceptionInterface {

	use CliExceptionTrait;

	/**
	 * @param string $message
	 * @param int    $exit_code
	 */
	public function __construct( $message = "", $exit_code = 3, ?\Throwable $previous = null ) {
		parent::__construct($message, $exit_code, $previous);
	}

}
