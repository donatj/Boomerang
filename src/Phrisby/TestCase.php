<?php

namespace Phrisby;

use Phrisby\Exceptions\ExpectFailedException;

class TestCase {

	private $response;
	private $header_sets;
	private $exceptions = array();

	public function __construct( $response, array $response_headers = null ) {
		$this->response    = $response;
		$this->header_sets = $response_headers;
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

		if( $match[1] != $status ) {
			$this->exceptions[] = new ExpectFailedException( 'Expected status ' . var_dump($match[1]) . ' to match ' . $status );
		}

		return $this;
	}

	private function hopHeaders( $hop = null ) {
		if( $hop === null ) {
			return end($this->header_sets);
		}

		return $this->header_sets[$hop];
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function expectHeader( $key, $value, $hop = null ) {
		$headers = $this->hopHeaders($hop);

		return $this;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function expectHeaderContains( $key, $value, $hop = null ) {
		$headers = $this->hopHeaders($hop);

		return $this;
	}

	public function expectBody( $value ) {

		return $this;
	}

	public function expectBodyContains( $value ) {

		return $this;
	}

	private function output() {

	}

}