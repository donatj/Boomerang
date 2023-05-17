<?php

namespace Boomerang;

use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\InfoResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\ResponseInterface;

/**
 * JSON Validator
 *
 * Used to validate JSON encoding and structure.
 */
class JSONValidator extends StructureValidator {

	/**
	 * @param ResponseInterface $response The response to inspect
	 */
	public function __construct( ResponseInterface $response ) {
		parent::__construct($response);

		$result = false;
		if( $error = $this->jsonDecode($response->getBody(), $result) ) {
			$this->expectations[] = new FailingResult($this, "Failed to Parse JSON Document: " . $error);
			$this->data           = null;
		} else {
			$this->expectations[] = new PassingResult($this, "Successfully Parsed Document");
			$this->data           = $result;
		}
	}

	private function jsonDecode( $json, &$result ) : ?string {
		try {
			$result = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		} catch( \JsonException $e ) {
			return $e->getMessage();
		}

		return null;
	}

	/**
	 * Log the JSON response as an InfoResult in the output.
	 *
	 * @return $this
	 */
	public function inspectJSON() : self {
		$this->expectations[] = new InfoResult($this, json_encode($this->data));

		return $this;
	}

}
