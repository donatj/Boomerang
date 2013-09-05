<?php

namespace Boomerang\Interfaces;

interface ExpectationResult {

	public function __construct( Validator $validator, $message = null );

	/**
	 * @return string
	 */
	public function getMessage();

	/**
	 * @return \Boomerang\Interfaces\Validator
	 */
	public function getValidator();

	/**
	 * @return bool
	 */
	public function getFail();

}