<?php

namespace Boomerang;

use Boomerang\Exceptions\ExpectFailedException;

class Response {

	public static $exceptions = array();
	private $response;
	private $header_sets;
	private $request;

	public function __construct( $response, array $response_headers = null, Request $request = null ) {
		$this->response    = $response;
		$this->header_sets = $response_headers;

		$this->request = $request;
	}

	/**
	 * @param int  $status
	 * @param null $hop
	 * @return $this
	 * @throws Exceptions\ExpectFailedException
	 */
	public function expectStatus( $status = 200, $hop = null ) {
		$headers = $this->hopHeaders($hop);

		preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $headers[0], $match);
		$match[1] = intval($match[1]);

		if( $match[1] != $status ) {
			self::$exceptions[] = new ExpectFailedException("Unexpected HTTP Status " . PHP_EOL .
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
		$headers = $this->hopHeaders($hop);

		if( !isset($headers[$key]) || $headers[$key] != $value ) {
			self::$exceptions[] = new ExpectFailedException('Unexpected Header Exact Match: ' . var_export($key, true) . PHP_EOL .
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
		$headers = $this->hopHeaders($hop);

		if( !isset($headers[$key]) || strpos($headers[$key], $value) === false ) {
			self::$exceptions[] = new ExpectFailedException('Unexpected Header Contains: ' . var_export($key, true) . PHP_EOL .
			" # Expected: " . var_export($value, true) . PHP_EOL .
			" # Actual: " . var_export($headers[$key], true), $this);
		}

		return $this;
	}

	public function expectBody( $response ) {

		if( $this->response != $response ) {
			self::$exceptions[] = new ExpectFailedException('Unexpected body: ' . PHP_EOL .
			" # Expected: " . var_export($response, true) . PHP_EOL .
			" # Actual: " . var_export($this->response, true), $this);
		}

		return $this;
	}

	public function expectBodyContains( $response ) {

		if( $this->response != $response ) {
			self::$exceptions[] = new ExpectFailedException('Unexpected body: ' . PHP_EOL .
			" # Expected: " . var_export($response, true) . PHP_EOL .
			" # Actual: " . var_export($this->response, true), $this);
		}

		return $this;
	}

	public function getRequest() {
		return $this->request;
	}

	private function hopHeaders( $hop = null ) {
		if( $hop === null ) {
			return end($this->header_sets);
		}

		return $this->header_sets[$hop];
	}

	public function getJSONValidator() {
		return new JSONValidator( $this->response, $this );
	}

}