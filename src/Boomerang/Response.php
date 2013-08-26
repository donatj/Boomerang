<?php

namespace Boomerang;

class Response {

	private $content;
	private $headers_raw;
	private $header_sets;
	private $request;

	/**
	 * @param string  $response
	 * @param string  $headers
	 * @param Request $request
	 */
	public function __construct( $response, $headers, Request $request = null ) {
		$this->content    = $response;
		$this->headers_raw = $headers;

		$headers_split = explode("\r\n\r\n", trim($headers));
		foreach( $headers_split as &$h ) {
			$h = $this->parseHeaders($h);
		}

		$this->header_sets = $headers_split;

		$this->request = $request;
	}

	private function parseHeaders( $raw_headers ) {
		$headers = array();
		$key     = '';

		foreach( explode("\n", $raw_headers) as $i => $h ) {
			$h = explode(':', $h, 2);

			if( isset($h[1]) ) {
				if( !isset($headers[$h[0]]) ) {
					$headers[$h[0]] = trim($h[1]);
				} elseif( is_array($headers[$h[0]]) ) {
					$headers[$h[0]] = array_merge($headers[$h[0]], array( trim($h[1]) ));
				} else {
					$headers[$h[0]] = array_merge(array( $headers[$h[0]] ), array( trim($h[1]) ));
				}

				$key = $h[0];
			} else {
				if( substr($h[0], 0, 1) == "\t" )
					$headers[$key] .= "\r\n\t" . trim($h[0]);
				elseif( !$key )
					$headers[0] = trim($h[0]);
				trim($h[0]);
			}
		}

		return $headers;
	}

	/**
	 * @return string
	 */
	public function getRawHeaders() {
		return $this->headers_raw;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	public function getRequest() {
		return $this->request;
	}

	public function getHeaders( $hop = null ) {
		if( $hop === null ) {
			return end($this->header_sets);
		}

		return $this->header_sets[$hop];
	}

}