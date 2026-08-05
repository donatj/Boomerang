<?php

namespace Boomerang\Interfaces;

interface ValidatorInterface {

	/**
	 * @return ExpectationResultInterface[]
	 */
	public function getExpectationResults();

	/**
	 * @return ResponseInterface
	 */
	public function getResponse();

}
