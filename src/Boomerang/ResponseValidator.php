<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;

class ResponseValidator implements Validator {

	private $expectations = array();
	/**
	 * @var Response
	 */
	private $response;

	public function __construct( Response $response ) {
		$this->response = $response;
	}

	/**
	 * @return \Boomerang\Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param int  $status
	 * @param null $hop
	 * @return $this
	 */
	public function expectStatus( $status = 200, $hop = null ) {
		$headers = $this->response->getHeaders($hop);

		preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $headers[0], $match);
		$match[1] = intval($match[1]);

		if( $match[1] != $status ) {
			$this->expectations[] = new ExpectResult(true, $this, "Unexpected HTTP Status", $status, $match[1]);
		} else {
			$this->expectations[] = new ExpectResult(false, $this);
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
		$headers = $this->response->getHeaders($hop);

		if( !isset($headers[$key]) || $headers[$key] != $value ) {
			$this->expectations[] = new ExpectResult(true, $this, 'Unexpected Header Exact Match: ' . var_export($key, true), $value, $headers[$key]);
		} else {
			$this->expectations[] = new ExpectResult(false, $this);
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
		$headers = $this->response->getHeaders($hop);

		if( !isset($headers[$key]) || strpos($headers[$key], $value) === false ) {
			$this->expectations[] = new ExpectResult(true, $this, 'Unexpected Header Contains: ' . var_export($key, true), $value, $headers[$key]);
		} else {
			$this->expectations[] = new ExpectResult(false, $this);
		}

		return $this;
	}

	/**
	 * @param string $response
	 * @return $this
	 */
	public function expectBody( $response ) {

		if( $this->response != $response ) {
			$this->expectations[] = new ExpectResult(true, $this, 'Unexpected body: ', $response, $this->response);
		} else {
			$this->expectations[] = new ExpectResult(false, $this);
		}

		return $this;
	}

	/**
	 * @param string $response
	 * @return $this
	 */
	public function expectBodyContains( $response ) {

		if( $this->response != $response ) {
			$this->expectations[] = new ExpectResult(true, $this, 'Unexpected body: ', $response, $this->response);
		} else {
			$this->expectations[] = new ExpectResult(false, $this);
		}

		return $this;
	}

	public function getExpectationResults() {
		return $this->expectations;
	}

}