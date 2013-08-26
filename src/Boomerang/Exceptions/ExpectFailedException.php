<?php

namespace Boomerang\Exceptions;

use Boomerang\Response;

class ExpectFailedException extends \Exception {

	private $test;

	public function __construct($message, Response $test, $code = 0) {
		parent::__construct($message, $code);
		$this->test = $test;
	}


	public function getTest() {
		return $this->test;
	}



}