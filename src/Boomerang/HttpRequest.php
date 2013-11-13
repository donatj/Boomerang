<?php

namespace Boomerang;

use Boomerang\Factories\HttpResponseFactory;

class HttpRequest {


	public $info;

	private $tmp = "/tmp";
	private $maxRedirects = 10;
	private $headers = array();
	private $endpoint;
	private $cookies = array();
	private $cookiesFollowRedirects = false;
	private $postdata = array();
	private $lastRequestTime = null;
	/**
	 * @var HttpResponseFactory
	 */
	private $responseFactory;

	public function __construct( $endpoint, HttpResponseFactory $responseFactory = null ) {
		$this->endpoint = $endpoint;

		if( $responseFactory === null ) {
			$this->responseFactory = new HttpResponseFactory();
		}
	}

	/**
	 * Retrieve an outgoing header by name
	 *
	 * @param string $key The name of the header.
	 * @return string|null
	 */
	public function getHeader( $key ) {
		return isset($this->headers[$key]) ? $this->headers[$key] : null;
	}

	/**
	 * Set an outgoing header by name.
	 *
	 * @param string $key The name of the header.
	 * @param string $value The value to set the header to.
	 */
	public function setHeader( $key, $value ) {
		$this->headers[$key] = $value;
	}

	/**
	 * @param $key
	 * @return string|null
	 */
	public function getPost( $key ) {
		return isset($this->postdata[$key]) ? $this->postdata[$key] : null;
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
	 * Retrieve all enqueued post-data as an array.
	 *
	 * @return array
	 */
	public function getPostdata() {
		return $this->postdata;
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
	 * Allows you to enable cookie's set by server re-posting following a redirect.
	 *
	 * Requires file system storage of a "cookie jar" file and is therefore disabled by default.
	 *
	 * @param bool   $bool
	 * @param string $tmp_path Path to save the cookie jar file, defaults to /tmp
	 */
	public function setCookiesFollowRedirects( $bool, $tmp_path = '/tmp' ) {
		$this->tmp                    = $tmp_path;
		$this->cookiesFollowRedirects = $bool;
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
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postdata, '', '&'));
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

		$startTime = microtime(true);
		$response  = curl_exec($ch);

		$this->lastRequestTime = microtime(true) - $startTime;

		$this->info = curl_getinfo($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers     = substr($response, 0, $header_size);

		$body = substr($response, $header_size);

		curl_close($ch);

		return $this->responseFactory->newInstance($body, $headers, $this);
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
	 * Get the time the last request took in seconds a float
	 *
	 * @return null|float
	 */
	public function getLastRequestTime() {
		return $this->lastRequestTime;
	}

	/**
	 * Set the maximum number of redirects(hops) a request should follow.
	 *
	 * @param int $maxRedirects
	 */
	public function setMaxRedirects( $maxRedirects ) {
		$this->maxRedirects = $maxRedirects;
	}

}