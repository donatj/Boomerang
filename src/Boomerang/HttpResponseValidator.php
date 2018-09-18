<?php

namespace Boomerang;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\PassingExpectationResult;
use Boomerang\Interfaces\HttpResponseInterface;

/**
 * HTTP Validation
 *
 * Used to validate expected responses, headers and HTTP statues
 *
 * @package Boomerang
 */
class HttpResponseValidator extends AbstractValidator {

	/**
	 * @var HttpResponseInterface
	 */
	private $response;

	public function __construct( HttpResponseInterface $response ) {
		$this->response = $response;
	}

	/**
	 * @return HttpResponseInterface
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Verify that the HTTP response code is as expected.
	 *
	 * @param int  $expected_status
	 * @param null $hop The zero indexed redirect hop. Defaults to the final hop.
	 * @return $this
	 */
	public function expectStatus( $expected_status = 200, $hop = null ) {

		$status = $this->response->getStatus($hop);

		if( $status != $expected_status ) {
			$this->expectations[] = new FailingExpectationResult($this, "Unexpected HTTP Status", $expected_status, $status);
		} else {
			$this->expectations[] = new PassingExpectationResult($this, "Expected HTTP Status", $status);
		}

		return $this;
	}

	/**
	 * Verify that a header field equals an expected value.
	 *
	 * @param string   $key The header field name to verify.
	 * @param string   $value The expected value.
	 * @param int|null $hop The zero indexed redirect hop. Defaults to the final hop.
	 * @return $this
	 */
	public function expectHeader( $key, $value, $hop = null ) {
		$header = $this->response->getHeader($key, $hop);

		if( $header != $value ) {
			$this->expectations[] = new FailingExpectationResult($this, "Unexpected header exact match: " . var_export($key, true), $value, $header);
		} else {
			$this->expectations[] = new PassingExpectationResult($this, "Expected header exact match: " . var_export($key, true), $header);
		}

		return $this;
	}

	/**
	 * Verify that a header field contains an expected value.
	 *
	 * For example, checking the header Content-Type for "json" **would** match a response of "application/json"
	 *
	 * @param string   $key The header field name to verify.
	 * @param string   $value The expected containing value.
	 * @param int|null $hop The zero indexed redirect hop. Defaults to the final hop.
	 * @return $this
	 */
	public function expectHeaderContains( $key, $value, $hop = null ) {
		$header = $this->response->getHeader($key, $hop);

		if( !$header || !$value || strpos($header, $value) === false ) {
			$this->expectations[] = new FailingExpectationResult($this, "Unexpected header contains: " . var_export($key, true), $value, $header);
		} else {
			$this->expectations[] = new PassingExpectationResult($this, "Expected header contains: " . var_export($key, true), $header);
		}

		return $this;
	}

	/**
	 * Verify that the content body equals an expected value.
	 *
	 * @param string $expectedContent The expected value.
	 * @return $this
	 */
	public function expectBody( $expectedContent ) {
		$content = $this->response->getBody();

		if( $content != $expectedContent ) {
			$this->expectations[] = new FailingExpectationResult($this, "Unexpected body", $expectedContent, $content);
		} else {
			$this->expectations[] = new PassingExpectationResult($this, "Expected body", $content);
		}

		return $this;
	}

	/**
	 * Verify that the content body contains an expected value.
	 *
	 * @param string $expectedContent The expected containing value.
	 * @return $this
	 */
	public function expectBodyContains( $expectedContent ) {
		$content = $this->response->getBody();

		if( strpos($content, $expectedContent) === false ) {
			$this->expectations[] = new FailingExpectationResult($this, 'Unexpected body contains', $expectedContent, $content);
		} else {
			$this->expectations[] = new PassingExpectationResult($this, 'Expected body contains', $expectedContent);
		}

		return $this;
	}

	/**
	 * Verify the number of redirection hops is as expected.
	 *
	 * @param int $expectedCount The expected number of redirect hops.
	 * @return $this
	 */
	public function expectHopCount( $expectedCount ) {
		$hops = $this->response->getHopCount();

		if( $hops != $expectedCount ) {
			$this->expectations[] = new FailingExpectationResult($this, 'Unexpected number of redirect hops', $expectedCount, $hops);
		} else {
			$this->expectations[] = new PassingExpectationResult($this, 'Expected number of redirect hops', $expectedCount);
		}

		return $this;
	}

}
