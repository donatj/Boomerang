<?php

namespace Boomerang;

class Response {

	private $response;
	private $header_sets;
	private $request;

	public function __construct( $response, array $response_headers = null, Request $request = null ) {
		$this->response    = $response;
		$this->header_sets = $response_headers;

		$this->request = $request;
	}

	public function getRequest() {
		return $this->request;
	}

	public function headers( $hop = null ) {
		if( $hop === null ) {
			return end($this->header_sets);
		}

		return $this->header_sets[$hop];
	}

}