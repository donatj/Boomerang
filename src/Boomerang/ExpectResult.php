<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;

class ExpectResult {

	/**
	 * @var bool
	 */
	private $fail;
	/**
	 * @var string|null
	 */
	private $message;
	/**
	 * @var Validator
	 */
	private $validator;

	/**
	 * @var bool
	 */
	private $presented = false;

	/**
	 * @param bool      $fail
	 * @param Validator $validator
	 * @param null      $message
	 */
	public function __construct( $fail, Validator $validator, $message = null ) {
		$this->fail      = $fail;
		$this->message   = $message;
		$this->validator = $validator;
	}

	/**
	 * @return Validator
	 */
	public function  getValidator() {
		return $this->validator;
	}

	/**
	 * @return string|null
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return bool
	 */
	public function getFail() {
		return $this->fail;
	}

	/**
	 * @param bool $presented
	 */
	public function setPresented($presented = true) {
		$this->presented = $presented;
	}

	public function getPresented() {
		return $this->presented;
	}

}