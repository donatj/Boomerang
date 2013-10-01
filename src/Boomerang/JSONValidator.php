<?php

namespace Boomerang;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\InfoResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\TypeExpectations\StructureEx;

class JSONValidator extends StructureValidator implements Interfaces\ResponseValidatorInterface {

	/**
	 * @var Response
	 */
	private $response;

	public function __construct( Response $response ) {
		$this->response = $response;

		$result = false;
		if( $error = $this->jsonDecode($response->getBody(), $result) ) {
			$this->expectations[] = new FailingResult($this, "Failed to Parse JSON Document");
			$result               = array();
		} else {
			$this->expectations[] = new PassingResult($this, "Successfully Parsed Document");
		}

		parent::__construct($result);
	}

	private function jsonDecode( $json, &$result ) {
		$result = json_decode($json, true);

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
				$error = 'Unknown JSON error occurred.';
				break;
		}

		return $error;
	}



	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	public function inspectJSON() {
		$this->expectations[] = new InfoResult($this, json_encode($this->data));

		return $this;
	}

}