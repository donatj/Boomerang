<?php

namespace Boomerang\Interfaces;

interface Validator {

	public function getExpectationResults();

	/**
	 * @return \Boomerang\Response
	 */
	public function getResponse();

}