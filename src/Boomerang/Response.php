<?php

namespace Boomerang;

class Response {

	private $body;
	private $headers_raw;
	private $header_sets;
	private $request;

	/**
	 * @param string  $body
	 * @param string  $headers
	 * @param Request $request
	 */
	public function __construct( $body, $headers, Request $request = null ) {
		$this->body        = $body;
		$this->headers_raw = $headers;

		$headers = $this->normalizeHeaders($headers);

		$headers_split = explode("\r\n\r\n", $headers);
		foreach( $headers_split as &$h ) {
			$h = $this->parseHeaders($h);
		}

		$this->header_sets = $headers_split;

		$this->request = $request;
	}

	/**
	 * Headers need to be \r\n by spec
	 *
	 * @param $s
	 * @return mixed
	 */
	private function normalizeHeaders( $s ) {
		$s = str_replace("\r\n", "\n", $s);
		$s = str_replace("\r", "\n", $s);
		$s = str_replace("\n", "\r\n", $s);
		trim($s);

		return $s;
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

		$headers = array_change_key_case($headers);

		return $headers;
	}

	/**
	 * @param string   $header
	 * @param null|int $hop
	 * @return null|string
	 */
	public function getHeader( $header, $hop = null ) {
		$headers = $this->getHeaders($hop);
		$header  = strtolower($header);
		if( isset($headers[$header]) ) {
			return $headers[$header];
		}

		return null;
	}

	/**
	 * @param null|int $hop
	 * @return array|null
	 */
	public function getHeaders( $hop = null ) {
		if( $hop === null ) {
			return end($this->header_sets);
		}

		if( isset($this->header_sets[$hop]) ) {
			return $this->header_sets[$hop];
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getAllHeaders() {
		return $this->header_sets;
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
	public function getBody() {
		return $this->body;
	}

	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return int
	 */
	public function getHopCount() {
		return count($this->header_sets);
	}

	/**
	 * @param int|null $hop
	 * @return int|null
	 */
	public function getStatus( $hop = null ) {
		$headers = $this->getHeaders($hop);

		if( $headers ) {
			preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $headers[0], $match);
			if( $status = intval($match[1]) ) {
				return $status;
			}
		}

		return null;
	}

}