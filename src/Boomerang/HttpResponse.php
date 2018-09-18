<?php

namespace Boomerang;

use Boomerang\Interfaces\HttpResponseInterface;

/**
 * Represents an HTTP Response.
 *
 * Usually received from an `HttpRequest` object
 *
 * @package Boomerang
 */
class HttpResponse implements HttpResponseInterface {

	/** @var string */
	private $body;

	/** @var string */
	private $headersRaw;

	/** @var array */
	private $headerSets;

	/** @var \Boomerang\HttpRequest|null */
	private $request;

	/**
	 * @param string           $body The body of the HTTP Request
	 * @param string           $headers
	 */
	public function __construct( $body, $headers, HttpRequest $request = null ) {
		$this->body       = $body;
		$this->headersRaw = $headers;

		$headers = $this->normalizeHeaders($headers);

		$headers_split = explode("\r\n\r\n", $headers);
		foreach( $headers_split as &$h ) {
			$h = $this->parseHeaders($h);
		}

		$this->headerSets = $headers_split;

		$this->request = $request;
	}

	/**
	 * Headers need to be \r\n by spec
	 *
	 * @param string $s
	 * @return string
	 */
	private function normalizeHeaders( $s ) {
		$s = str_replace("\r\n", "\n", $s);
		$s = str_replace("\r", "\n", $s);
		$s = str_replace("\n", "\r\n", $s);

		return trim($s);
	}

	/**
	 * @param string $rawHeaders
	 * @return string[]
	 */
	private function parseHeaders( $rawHeaders ) {
		$headers = [];
		$key     = '';

		foreach( explode("\n", $rawHeaders) as $i => $h ) {
			$h = explode(':', $h, 2);

			if( isset($h[1]) ) {
				if( !isset($headers[$h[0]]) ) {
					$headers[$h[0]] = trim($h[1]);
				} elseif( is_array($headers[$h[0]]) ) {
					$headers[$h[0]] = array_merge($headers[$h[0]], [ trim($h[1]) ]);
				} else {
					$headers[$h[0]] = array_merge([ $headers[$h[0]] ], [ trim($h[1]) ]);
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

		return array_change_key_case($headers);
	}

	/**
	 * Get a response header by name.
	 *
	 * @param string   $header
	 * @param int|null $hop
	 * @return string|null Header value or null on not found
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
	 * Get response headers as a HeaderName => Value array
	 *
	 * @param int|null $hop The zero indexed hop(redirect). Defaults to the final hop.
	 * @return array|null
	 */
	public function getHeaders( $hop = null ) {
		if( $hop === null ) {
			return end($this->headerSets);
		}

		if( isset($this->headerSets[$hop]) ) {
			return $this->headerSets[$hop];
		}

		return null;
	}

	/**
	 * Get all response headers from all hops as a HopIndex => HeaderName => Value array.
	 *
	 * Note: header key values are lower cased.
	 *
	 * @return array
	 */
	public function getAllHeaders() {
		return $this->headerSets;
	}

	/**
	 * Get the raw un-parsed Response header string.
	 *
	 * @return string
	 */
	public function getRawHeaders() {
		return $this->headersRaw;
	}

	/**
	 * Get the body of the Response.
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * Get the HttpRequest object that made the Response object.
	 *
	 * @return HttpRequest
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Get the number of hops(redirects) the request took
	 *
	 * @return int
	 */
	public function getHopCount() {
		return count($this->headerSets);
	}

	/**
	 * Get the HTTP status of a hop
	 *
	 * @param int|null $hop The zero indexed hop(redirect). Defaults to the final hop.
	 * @return int|null
	 */
	public function getStatus( $hop = null ) {
		$headers = $this->getHeaders($hop);

		if( $headers && isset($headers[0]) ) {
			preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $headers[0], $match);
			if( $status = intval($match[1]) ) {
				return $status;
			}
		}

		return null;
	}

}
