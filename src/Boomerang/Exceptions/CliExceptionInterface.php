<?php

namespace Boomerang\Exceptions;

interface CliExceptionInterface extends BoomerangException {

	public function getExitCode() : int;

}
