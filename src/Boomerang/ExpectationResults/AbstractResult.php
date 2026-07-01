<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ValidatorInterface;

abstract class AbstractResult implements ExpectationResultInterface {

	/**
	 * @var string|null
	 */
	protected $message;

	/**
	 * @var ValidatorInterface
	 */
	protected $validator;

	/**
	 * @param ValidatorInterface $validator
	 * @param null|string        $message
	 */
	public function __construct( ValidatorInterface $validator, $message = null ) {
		$this->message   = $message;
		$this->validator = $validator;
	}

	public function getMessage() {
		return $this->message ?? '';
	}

	public function getValidator() {
		return $this->validator;
	}

	public function getFail() {
		return false;
	}

}