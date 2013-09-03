<?php

namespace Boomerang\ExpectationResults;

use Boomerang\Interfaces\ExpectationResult;
use Boomerang\Interfaces\Validator;

class MessageResult implements ExpectationResult {

	/**
	 * @var string
	 */
	protected $message;
	/**
	 * @var Validator
	 */
	protected $validator;

	function __construct( Validator $validator, $message = null ) {
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
	 * @return Validator
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