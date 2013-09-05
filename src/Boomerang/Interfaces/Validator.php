<?php

namespace Boomerang\Interfaces;

interface Validator {

	/**
	 * @return array
	 */
	public function getExpectationResults();

	/**
	 * @return \Boomerang\Response
	 */
	public function getResponse();

}