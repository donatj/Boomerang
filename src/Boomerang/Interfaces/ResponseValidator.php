<?php

namespace Boomerang\Interfaces;

interface ResponseValidator extends Validator {

	/**
	 * @return \Boomerang\Response
	 */
	public function getResponse();

}