<?php

namespace Boomerang\Interfaces;

interface ResponseValidatorInterface extends ValidatorInterface {

	/**
	 * @return \Boomerang\HttpResponse
	 */
	public function getResponse();

}
