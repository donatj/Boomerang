<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;

class JSONValidator implements Validator {

	private $expectations = array();
	private $result;

	public function __construct( Response $response ) {

		$result = false;
		if( $error = $this->jsonDecode($response->getBody(), $result) ) {
			$this->expectations[] = new ExpectResult(true, $this, "Failed to Parse JSON Document");
			$this->result         = array();
		} else {
			$this->expectations[] = new ExpectResult(false, $this);
			$this->result         = $result;
		}

	}

	private function jsonDecode( $json, &$result ) {
		// decode the JSON data
		$result = json_decode($json, true);

		// switch and check possible JSON errors
		switch( json_last_error() ) {
			case JSON_ERROR_NONE:
				$error = false; // JSON is valid
				break;
			case JSON_ERROR_DEPTH:
				$error = 'Maximum stack depth exceeded.';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$error = 'Underflow or the modes mismatch.';
				break;
			case JSON_ERROR_CTRL_CHAR:
				$error = 'Unexpected control character found.';
				break;
			case JSON_ERROR_SYNTAX:
				$error = 'Syntax error, malformed JSON.';
				break;
			// only PHP 5.3+
			case JSON_ERROR_UTF8:
				$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
				break;
			default:
				$error = 'Unknown JSON error occured.';
				break;
		}

		return $error;
	}

	public function getExpectationResults() {
		return $this->expectations;
	}

}