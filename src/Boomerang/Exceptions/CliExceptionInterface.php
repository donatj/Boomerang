<?php

namespace Boomerang\Exceptions;

interface CliExceptionInterface {

	/**
	 * @return int
	 */
	public function getExitCode();

}
