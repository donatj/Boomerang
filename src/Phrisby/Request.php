<?php

namespace Phrisby;

class Request {

	public $tmp = "/tmp";
	public $max_redirects = 10;
	private $headers = array();
	private $endpoint;
	private $cookies = array();
	private $cookiesFollowRedirects = false;

	public function __construct( $endpoint ) {
		$this->endpoint = $endpoint;
	}

	public function setHeader( $key, $value ) {
		$this->headers[$key] = $value;
	}

	/**
	 * @param bool $bool
	 */
	public function setCookiesFollowRedirects( $bool ) {
		$this->cookiesFollowRedirects = $bool;
	}

	/**
	 * @return TestCase
	 */
	public function makeRequest() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		if( $this->cookiesFollowRedirects ) {
			$cookiejar = tempnam($this->tmp, 'PHB');
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
		}

		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

		if( $this->max_redirects ) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $this->max_redirects);
		}

		$response   = curl_exec($ch);
		$this->info = curl_getinfo($ch);

		$header_size   = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers       = substr($response, 0, $header_size);
		$headers_split = explode("\r\n\r\n", trim($headers));
		foreach( $headers_split as &$h ) {
			$h = $this->http_parse_headers($h);
		}

		$body = substr($response, $header_size);

		curl_close($ch);

		return new TestCase($body, $headers_split);
	}

	public function getHeaders() {
		$headers = $this->headers;
		if( !isset($headers['Accept']) ) {
			$headers['Accept'] = $this->detectAccept($this->endpoint);
		}

		return $headers;
	}

	public function setHeaders( array $headers ) {
		$this->headers = $headers;
	}

	private function detectAccept( $endpoint ) {
		$url  = parse_url($endpoint);
		$path = pathinfo($url['path']);
		switch( strtolower($path['extension']) ) {
			case 'json':
				return 'application/json';
				break;
			case 'xml':
				return 'application/xml';
			default:
				return 'application/json,application/xml,text/html,application/xhtml+xml,*/*';
				break;
		}

		return "application/json,application/xml,text/html,*/*";
	}

	private function http_parse_headers( $raw_headers ) {
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


}