<?php

namespace Boomerang\Interfaces;

interface ExpectationResultInterface {

	//	public function __construct( ValidatorInterface $validator, $message = null );

	public function getMessage() : ?string;

	public function getValidator() : ValidatorInterface;

	public function getFail() : bool;

}
