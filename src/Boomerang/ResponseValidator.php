<?php

namespace Boomerang;

use Boomerang\Exceptions\ExpectFailedException;
use Boomerang\Interfaces\Validator;

class ResponseValidator implements Validator {

	private $exceptions = array();

	/**
	 * @var Response
	 */
	private $response;

	public function __construct( Response $response ) {
		$this->response = $response;
	}

	/**
	 * @param int  $status
	 * @param null $hop
	 * @return $this
	 * @throws Exceptions\ExpectFailedException
	 */
	public function expectStatus( $status = 200, $hop = null ) {
		$headers = $this->response->headers($hop);

		preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $headers[0], $match);
		$match[1] = intval($match[1]);

		if( $match[1] != $status ) {
			$this->exceptions[] = new ExpectFailedException("Unexpected HTTP Status " . PHP_EOL .
			" # Expected: " . PHP_EOL . var_export($match[1], true) . PHP_EOL .
			' # Actual: ' . PHP_EOL . var_export($status, true), $this);
		}

		return $this;
	}

	/**
	 * @param string   $key
	 * @param string   $value
	 * @param null|int $hop
	 * @return $this
	 */
	public function expectHeader( $key, $value, $hop = null ) {
		$headers = $this->response->headers($hop);

		if( !isset($headers[$key]) || $headers[$key] != $value ) {
			$this->exceptions[] = new ExpectFailedException('Unexpected Header Exact Match: ' . var_export($key, true) . PHP_EOL .
			" # Expected: " . var_export($value, true) . PHP_EOL .
			" # Actual: " . var_export($headers[$key], true), $this);
		}

		return $this;
	}

	/**
	 * @param  string  $key
	 * @param   string $value
	 * @param null|int $hop
	 * @return $this
	 */
	public function expectHeaderContains( $key, $value, $hop = null ) {
		$headers = $this->response->headers($hop);

		if( !isset($headers[$key]) || strpos($headers[$key], $value) === false ) {
			$this->exceptions[] = new ExpectFailedException('Unexpected Header Contains: ' . var_export($key, true) . PHP_EOL .
			" # Expected: " . var_export($value, true) . PHP_EOL .
			" # Actual: " . var_export($headers[$key], true), $this);
		}

		return $this;
	}

	public function expectBody( $response ) {

		if( $this->response != $response ) {
			$this->exceptions[] = new ExpectFailedException('Unexpected body: ' . PHP_EOL .
			" # Expected: " . var_export($response, true) . PHP_EOL .
			" # Actual: " . var_export($this->response, true), $this);
		}

		return $this;
	}

	public function expectBodyContains( $response ) {

		if( $this->response != $response ) {
			$this->exceptions[] = new ExpectFailedException('Unexpected body: ' . PHP_EOL .
			" # Expected: " . var_export($response, true) . PHP_EOL .
			" # Actual: " . var_export($this->response, true), $this);
		}

		return $this;
	}

	public function getExceptions() {
		return $this->exceptions;
	}

}