<?php

namespace Boomerang;

class HttpRequest {

	public $tmp = "/tmp";
	public $maxRedirects = 10;
	public $info;
	private $headers = array();
	private $endpoint;
	private $cookies = array();
	private $cookiesFollowRedirects = false;
	private $postdata = array();

	public function __construct( $endpoint ) {
		$this->endpoint = $endpoint;
	}

	/**
	 * @param string
	 * @return string|null
	 */
	public function getHeader( $key ) {
		return isset($this->headers[$key]) ? $this->headers[$key] : null;
	}

	/**
	 * Set all post data, whipping past values.
	 *
	 * @param array $post
	 */
	public function setPostdata( array $post ) {
		$this->postdata = $post;
	}

	/**
	 * Set a named key of the post value
	 *
	 * @param $key
	 * @param $value
	 */
	public function setPost( $key, $value ) {
		$this->postdata[$key] = $value;
	}

	/**
	 * @param        $bool
	 * @param string $tmp_path
	 */
	public function setCookiesFollowRedirects( $bool, $tmp_path = '/tmp' ) {
		$this->tmp                    = $tmp_path;
		$this->cookiesFollowRedirects = $bool;
	}

	/**
	 * @return HttpResponse
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

		if( $this->postdata ) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata);
		}

		if( $this->cookies ) {
			curl_setopt($ch, CURLOPT_COOKIE, http_build_cookie($this->cookies));
		}

		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

		if( $this->maxRedirects ) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
		}

		$response   = curl_exec($ch);
		$this->info = curl_getinfo($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers     = substr($response, 0, $header_size);

		$body = substr($response, $header_size);

		curl_close($ch);

		return new HttpResponse($body, $headers, $this);
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		$headers = $this->headers;
		if( !isset($headers['Accept']) ) {
			$headers['Accept'] = $this->detectAccept($this->endpoint);
		}

		return $headers;
	}

	/**
	 * @param array $headers
	 */
	public function setHeaders( array $headers ) {
		$this->headers = $headers;
	}

	private function detectAccept( $endpoint ) {
		$url  = parse_url($endpoint);
		$path = pathinfo($url['path']);
		if( isset($path['extension']) ) {
			switch( strtolower($path['extension']) ) {
				case 'json':
					return 'application/json';
					break;
				case 'xml':
					return 'application/xml';
			}
		}

		return 'application/json,application/xml,text/html,application/xhtml+xml,*/*';
	}

	/**
	 * @param int $maxRedirects
	 */
	public function setMaxRedirects( $maxRedirects ) {
		$this->maxRedirects = $maxRedirects;
	}

	/**
	 * @param array $cookies
	 */
	public function setCookies( array $cookies ) {
		$this->cookies = $cookies;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function setCookie( $key, $value ) {
		$this->cookies[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function setHeader( $key, $value ) {
		$this->headers[$key] = $value;
	}

	/**
	 * @return string
	 */
	public function getEndpoint() {
		return $this->endpoint;
	}

	/**
	 * @param string $endpoint
	 */
	public function setEndpoint( $endpoint ) {
		$this->endpoint = $endpoint;
	}

}