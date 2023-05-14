<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ValidatorInterface;

abstract class AbstractResult implements ExpectationResultInterface {

	protected ?string $message;

	protected ValidatorInterface $validator;

	public function __construct( ValidatorInterface $validator, ?string $message = null ) {
		$this->message   = $message;
		$this->validator = $validator;
	}

	public function getMessage() : string {
		return $this->message;
	}

	public function getValidator() : ValidatorInterface {
		return $this->validator;
	}

	public function getFail() : bool {
		return false;
	}

}
