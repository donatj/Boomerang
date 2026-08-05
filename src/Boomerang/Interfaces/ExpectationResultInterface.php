<?php

namespace Boomerang\Interfaces;

interface ExpectationResultInterface {

	//	public function __construct( ValidatorInterface $validator, $message = null );

	/**
	 * @return string
	 */
	public function getMessage();

	/**
	 * @return \Boomerang\Interfaces\ValidatorInterface
	 */
	public function getValidator();

	/**
	 * @return bool
	 */
	public function getFail();

}
