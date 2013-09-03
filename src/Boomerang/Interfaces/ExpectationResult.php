<?php

namespace Boomerang\Interfaces;

use Boomerang\Interfaces\Validator;

interface ExpectationResult {

	public function __construct( Validator $validator, $message = null );

	/**
	 * @return String
	 */
	public function getMessage();

	/**
	 * @return Validator
	 */
	public function getValidator();

	/**
	 * @return bool
	 */
	public function getFail();

}