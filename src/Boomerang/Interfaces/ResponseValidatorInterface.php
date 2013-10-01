<?php

namespace Boomerang\Interfaces;

interface ResponseValidatorInterface extends ValidatorInterface {

	/**
	 * @return \Boomerang\Response
	 */
	public function getResponse();

}