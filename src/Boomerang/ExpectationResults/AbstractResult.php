<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ValidatorInterface;

abstract class AbstractResult implements ExpectationResultInterface {

	/**
	 * @var string
	 */
	protected $message;
	/**
	 * @var ValidatorInterface
	 */
	protected $validator;

	/**
	 * @param ValidatorInterface   $validator
	 * @param null|string $message
	 */
	public function __construct( ValidatorInterface $validator, $message = null ) {
		$this->message   = $message;
		$this->validator = $validator;
	}

	/**
	 * @return String
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return ValidatorInterface
	 */
	public function getValidator() {
		return $this->validator;
	}

	/**
	 * @return bool
	 */
	public function getFail() {
		return false;
	}

}