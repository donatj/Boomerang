<?php

namespace Phrisby;

class Request {

	public $max_redirects = 10;

	private $headers = array();
	private $endpoint;
	private $cookies = array();

	public function __construct($endpoint) {
		$this->endpoint = $endpoint;
	}

	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}

	public function setHeader($key, $value) {
		$this->headers[ $key ] = $value;
	}

	public function getHeaders() {
		$headers = $this->headers;
		if(!isset($headers['Accept'])) {
			
		}

	}

	public function makeRequest() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

		// curl_setopt($ch, CURLOPT_FILETIME, true);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		if($this->max_redirects) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $this->max_redirects);
		}
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
		$this->info = curl_getinfo($ch);
		
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		print_r($this->info);

		curl_close($ch);
	}

	private function detectAccept($endpoint) {

	}


}