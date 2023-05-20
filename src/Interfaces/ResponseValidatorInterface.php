<?php

namespace Boomerang\Interfaces;

interface ResponseValidatorInterface extends ValidatorInterface {

	public function getResponse() : ResponseInterface;

}
