<?php

namespace Boomerang\Exceptions;

use Boomerang\TestCase;

class ExpectFailedException extends \Exception {

	private $test;

	public function __construct($message, TestCase $test, $code = 0) {
		parent::__construct($message, $code);
		$this->test = $test;
	}


	public function getTest() {
		return $this->test;
	}



}