<?php

namespace Boomerang\Exceptions;

interface CliExceptionInterface extends BoomerangException {

	/**
	 * @return int
	 */
	public function getExitCode();

}
