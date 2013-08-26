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
//	/**
//	 * @var bool
//	 */
//	private $presented = false;
	/**
	 * @var null|string
	 */
	private $expected;
	/**
	 * @var null|string
	 */
	private $actual;

	/**
	 * @param             $fail
	 * @param Validator   $validator
	 * @param null|string $message
	 * @param null|string $expected
	 * @param null|string $actual
	 */
	public function __construct( $fail, Validator $validator, $message = null, $expected = null, $actual = null ) {
		$this->fail      = $fail;
		$this->message   = $message;
		$this->validator = $validator;
		$this->expected  = $expected;
		$this->actual    = $actual;
	}

	/**
	 * @return null|string
	 */
	public function getActual() {
		return $this->actual;
	}

	/**
	 * @return null|string
	 */
	public function getExpected() {
		return $this->expected;
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

//	public function getPresented() {
//		return $this->presented;
//	}
//
//	/**
//	 * @param bool $presented
//	 */
//	public function setPresented( $presented = true ) {
//		$this->presented = $presented;
//	}

}