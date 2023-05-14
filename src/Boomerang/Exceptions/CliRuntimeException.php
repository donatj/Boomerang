<?php

namespace Boomerang\Exceptions;

class CliRuntimeException extends \RuntimeException implements CliExceptionInterface {

	use CliExceptionTrait;

	public function __construct( string $message = "", int $exit_code = 3, ?\Exception $previous = null ) {
		parent::__construct($message, $exit_code, $previous);
	}

}
