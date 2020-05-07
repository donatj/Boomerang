<?php

namespace Boomerang;

use Boomerang\Factories\HttpResponseFactory;

/**
 * Utility for generating HTTP Requests and receiving Responses into `HttpResponse` objects.
 *
 * @package Boomerang
 */
class HttpRequest {

	const GET     = "GET";
	const POST    = "POST";
	const PUT     = "PUT";
	const PATCH   = "PATCH";
	const DELETE  = "DELETE";
	const TRACE   = "TRACE";
	const OPTIONS = "OPTIONS";

	private $curlInfo;

	/** @var string */
	private $tmp;

	private $maxRedirects = 10;
	private $headers = [];
	private $endpointParts;
	private $cookies = [];
	private $cookiesFollowRedirects = false;
	/** @var array|string */
	private $body = [];
	private $lastRequestTime = null;

	/**
	 * @var HttpResponseFactory
	 */
	private $responseFactory;

	/**
	 * @var string
	 */
	private $method = self::GET;

	/**
	 * @param string              $endpoint URI to request.
	 * @param HttpResponseFactory $responseFactory A factory for creating Response objects.
	 */
	public function __construct( $endpoint, HttpResponseFactory $responseFactory = null ) {
		$this->setEndpoint($endpoint);
		$this->tmp = sys_get_temp_dir() ?: '/tmp';

		if( $responseFactory === null ) {
			$this->responseFactory = new HttpResponseFactory();
		} else {
			$this->responseFactory = $responseFactory;
		}
	}

	/**
	 * Get the current request method.
	 *
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Set the request method.
	 *
	 * Helper constants exist, for example:
	 *
	 * ```php
	 * $req->setMethod(HttpRequest::POST);
	 * ```
	 *
	 * @param string $method
	 */
	public function setMethod( $method ) {
		$this->method = $method;
	}

	/**
	 * Retrieve a url param by name
	 *
	 * @param string $param The name of the param.
	 * @return array|string|null Null on failure.
	 */
	public function getUrlParam( $param ) {
		$params = $this->getUrlParams();

		return $params[$param] ?? null;
	}

	/**
	 * Set a url param by name.
	 *
	 * @param string                 $param The name of the param.
	 * @param array|float|int|string $value
	 */
	public function setUrlParam( $param, $value ) {
		$params         = $this->getUrlParams();
		$params[$param] = $value;

		$this->endpointParts['query'] = http_build_query($params);
	}

	/**
	 * Get all url params.
	 *
	 * @return array
	 */
	public function getUrlParams() {
		$query = !empty($this->endpointParts['query']) ? $this->endpointParts['query'] : '';
		parse_str($query, $result);

		return $result;
	}

	/**
	 * Set outgoing params as an array of ParamName => Value
	 *
	 * @param array $params Params to set
	 */
	public function setUrlParams( array $params ) {
		$this->endpointParts['query'] = http_build_query($params);
	}

	/**
	 * Retrieve an outgoing header by name
	 *
	 * @param string $key The name of the header.
	 * @return string|null Null on failure.
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
	 * Get all set headers.
	 *
	 * @return array
	 */
	public function getHeaders() {
		$headers = $this->headers;
		if( !isset($headers['Accept']) ) {
			$headers['Accept'] = $this->detectAccept($this->getEndpoint());
		}

		return $headers;
	}

	/**
	 * Set outgoing headers as an array of HeaderName => Value
	 *
	 * @param array $headers Headers to set
	 */
	public function setHeaders( array $headers ) {
		$this->headers = $headers;
	}

	/**
	 * Set outgoing basic auth header.
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function setBasicAuth( $username, $password = '' ) {
		$this->setHeader('Authorization', "Basic " . base64_encode("{$username}:{$password}"));
	}

	/**
	 * Retrieve an post value by name.
	 *
	 * @deprecated Use getFormValue instead
	 *
	 * @param $key
	 * @return string|null
	 */
	public function getPost( $key ) {
		return isset($this->body[$key]) ? $this->body[$key] : null;
	}

	/**
	 * Set a named key of the post value
	 *
	 * Note that this has the side effect of changing the HTTP Method to POST
	 *
	 * @deprecated Use setMethod and setFormValue instead
	 *
	 * @param string $key
	 */
	public function setPost( $key, $value ) {
		$this->method = self::POST;
		$this->setFormValue($key, $value);
	}

	/**
	 * Set a named key of the form values
	 *
	 * Note that if there is a non-form body set, this will replace it.
	 *
	 * @param string $key
	 */
	public function setFormValue( $key, $value ) {
		if( !is_array($this->body) ) {
			$this->body = [];
		}
		$this->body[$key] = $value;
	}

	/**
	 * Retrieve an form value by name.
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	public function getFormValue( $key ) {
		return isset($this->body[$key]) ? $this->body[$key] : null;
	}

	/**
	 * Retrieve all queued post-data as an array.
	 *
	 * @deprecated Use getBody instead
	 *
	 * @return array|string
	 */
	public function getPostData() {
		return $this->getBody();
	}

	/**
	 * Set all post data, whipping past values.
	 *
	 * Note that this has the side effect of changing the HTTP Method to POST
	 *
	 * @deprecated Use setBody instead
	 */
	public function setPostData( array $post ) {
		$this->method = self::POST;
		$this->setBody($post);
	}

	/**
	 * Get the requests body
	 *
	 * @return array|string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * Set the requests body
	 *
	 * @param array|string $body
	 */
	public function setBody( $body ) {
		$this->body = $body;
	}

	/**
	 * Allows you to enable cookie's set by server re-posting following a redirect.
	 *
	 * Requires file system storage of a "cookie jar" file and is therefore disabled by default.
	 *
	 * @param bool $bool true/false to enable/disable respectively
	 */
	public function setCookiesFollowRedirects( $bool ) {
		$this->cookiesFollowRedirects = $bool;
	}

	/**
	 * Set outgoing cookies as an array of CookieName => Value
	 *
	 * @param array $cookies Cookies to set
	 */
	public function setCookies( array $cookies ) {
		$this->cookies = $cookies;
	}

	/**
	 * Set a named cookies outgoing value
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function setCookie( $key, $value ) {
		$this->cookies[$key] = $value;
	}

	/**
	 * @return string
	 */
	private function composeUrl( array $parsed_url ) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = $parsed_url['host'] ?? '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = $parsed_url['user'] ?? '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
		$pass     = ($user || $pass) ? "{$pass}@" : '';
		$path     = $parsed_url['path'] ?? '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

		return "{$scheme}{$user}{$pass}{$host}{$port}{$path}{$query}{$fragment}";
	}

	/**
	 * Gets the request URI
	 *
	 * @return string
	 */
	public function getEndpoint() {
		return $this->composeUrl($this->endpointParts);
	}

	/**
	 * Sets the request URI
	 *
	 * @param string $endpoint
	 */
	public function setEndpoint( $endpoint ) {
		$parts = parse_url($endpoint);
		if( $parts === false ) {
			throw new \InvalidArgumentException("Failed to parse url '{$endpoint}'");
		}
		$this->endpointParts = $parts;
	}

	/**
	 * Execute the request
	 *
	 * @return HttpResponse An object representing the result of the request
	 */
	public function makeRequest() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getEndpoint());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		if( $this->cookiesFollowRedirects ) {
			$cookiejar = tempnam($this->tmp, 'PHB');
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
		}

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

		if( $this->body ) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
		}

		if( count($this->cookies) > 0 ) {
			// @todo This does not work without http extension… find a better solution
			curl_setopt($ch, CURLOPT_COOKIE, http_build_cookie($this->cookies));
		}

		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getFlatHeaders());

		if( $this->maxRedirects ) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
		}

		$startTime = microtime(true);
		$response  = curl_exec($ch);

		$this->lastRequestTime = microtime(true) - $startTime;

		$this->curlInfo = curl_getinfo($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers     = substr($response, 0, $header_size);

		$body = substr($response, $header_size);

		curl_close($ch);

		return $this->responseFactory->newInstance($body, $headers, $this);
	}

	/**
	 * @param string $endpoint
	 * @return string
	 */
	private function detectAccept( $endpoint ) {
		$url = parse_url($endpoint);
		if( isset($url['path']) ) {
			$path = pathinfo($url['path']);
			if( isset($path['extension']) ) {
				switch( strtolower($path['extension']) ) {
					case 'json':
						return 'application/json';
					case 'xml':
						return 'application/xml';
				}
			}
		}

		return 'application/json,application/xml,text/html,application/xhtml+xml,*/*';
	}

	/**
	 * Gets headers as a flattened array for cURL $key => $val --> $key: $val
	 *
	 * @return array
	 */
	private function getFlatHeaders() {
		$output = [];
		foreach( $this->getHeaders() as $key => $value ) {
			$output[] = "$key: $value";
		}

		return $output;
	}

	public function getCurlInfo() {
		return $this->curlInfo;
	}

	/**
	 * Get the time the last request took in seconds a float
	 *
	 * @return float|null null if there is no last request
	 */
	public function getLastRequestTime() {
		return $this->lastRequestTime;
	}

	/**
	 * Get the current maximum number of redirects(hops) a request should follow.
	 *
	 * @return int
	 */
	public function getMaxRedirects() {
		return $this->maxRedirects;
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
