<?php

namespace Boomerang;

use Boomerang\Exceptions\InvalidArgumentException;
use Boomerang\Exceptions\ResponseException;
use Boomerang\Factories\HttpResponseFactory;
use Boomerang\Interfaces\HttpResponseInterface;
use Http\Discovery\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Utility for generating HTTP Requests and receiving Responses into `HttpResponse` objects.
 */
class HttpRequest {

	public const GET     = "GET";
	public const POST    = "POST";
	public const PUT     = "PUT";
	public const PATCH   = "PATCH";
	public const DELETE  = "DELETE";
	public const TRACE   = "TRACE";
	public const OPTIONS = "OPTIONS";

	/** @var array|false|null */
	private $curlInfo;

	private string $tmp;

	private int $maxRedirects = 10;
	private array $headers = [];
	private array $endpointParts;
	private array $cookies = [];
	private bool $cookiesFollowRedirects = false;
	/** @var array|string */
	private $body = [];
	private ?float $lastRequestTime = null;

	private HttpResponseFactory $responseCollectionFactory;

	private string $method = self::GET;

	private ResponseFactoryInterface $responseFactory;

	private StreamFactoryInterface $streamFactory;

	/**
	 * @param string $endpoint URI to request.
	 */
	public function __construct(
		string $endpoint,
		?ResponseFactoryInterface $responseFactory = null,
		?StreamFactoryInterface $streamFactory = null,
		?HttpResponseFactory $responseCollectionFactory = null
	) {
		$this->setEndpoint($endpoint);
		$this->tmp = sys_get_temp_dir() ?: '/tmp';

		$this->responseFactory           = $responseFactory ?? new Psr17Factory;
		$this->streamFactory             = $streamFactory ?? new Psr17Factory;
		$this->responseCollectionFactory = $responseCollectionFactory ?? new HttpResponseFactory;
	}

	/**
	 * Get the current request method.
	 */
	public function getMethod() : string {
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
	 */
	public function setMethod( string $method ) : void {
		$this->method = $method;
	}

	/**
	 * Retrieve a url param by name
	 *
	 * @param string $param The name of the param.
	 * @return array|string|null Null on failure.
	 */
	public function getUrlParam( string $param ) {
		$params = $this->getUrlParams();

		return $params[$param] ?? null;
	}

	/**
	 * Set a url param by name.
	 *
	 * @param string                 $param The name of the param.
	 * @param array|float|int|string $value
	 */
	public function setUrlParam( string $param, $value ) : void {
		$params         = $this->getUrlParams();
		$params[$param] = $value;

		$this->endpointParts['query'] = http_build_query($params);
	}

	/**
	 * Get all url params.
	 */
	public function getUrlParams() : array {
		$query = !empty($this->endpointParts['query']) ? $this->endpointParts['query'] : '';
		parse_str($query, $result);

		return $result;
	}

	/**
	 * Set outgoing params as an array of ParamName => Value
	 *
	 * @param array $params Params to set
	 */
	public function setUrlParams( array $params ) : void {
		$this->endpointParts['query'] = http_build_query($params);
	}

	/**
	 * Retrieve an outgoing header by name
	 *
	 * @param string $key The name of the header.
	 * @return string|null Null on failure.
	 */
	public function getHeader( string $key ) : ?string {
		return $this->headers[$key] ?? null;
	}

	/**
	 * Set an outgoing header by name.
	 *
	 * @param string $key   The name of the header.
	 * @param string $value The value to set the header to.
	 */
	public function setHeader( string $key, string $value ) : void {
		$this->headers[$key] = $value;
	}

	/**
	 * Get all set headers.
	 */
	public function getHeaders() : array {
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
	public function setHeaders( array $headers ) : void {
		$this->headers = $headers;
	}

	/**
	 * Set outgoing basic auth header.
	 */
	public function setBasicAuth( string $username, string $password = '' ) : void {
		$this->setHeader('Authorization', "Basic " . base64_encode("{$username}:{$password}"));
	}

	/**
	 * Set a named key of the form values
	 *
	 * Note that if there is a non-form body set, this will replace it.
	 */
	public function setFormValue( string $key, $value ) : void {
		if( !is_array($this->body) ) {
			$this->body = [];
		}

		$this->body[$key] = $value;
	}

	/**
	 * Retrieve a form value by name.
	 *
	 * @return mixed|null
	 */
	public function getFormValue( string $key ) {
		return $this->body[$key] ?? null;
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
	public function setBody( $body ) : void {
		$this->body = $body;
	}

	/**
	 * Allows you to enable cookie's set by server re-posting following a redirect.
	 *
	 * Requires file system storage of a "cookie jar" file and is therefore disabled by default.
	 *
	 * @param bool $bool true/false to enable/disable respectively
	 */
	public function setCookiesFollowRedirects( bool $bool ) : void {
		$this->cookiesFollowRedirects = $bool;
	}

	/**
	 * Set outgoing cookies as an array of CookieName => Value
	 *
	 * @param array $cookies Cookies to set
	 */
	public function setCookies( array $cookies ) : void {
		$this->cookies = $cookies;
	}

	/**
	 * Set a named cookies outgoing value
	 */
	public function setCookie( string $key, string $value ) : void {
		$this->cookies[$key] = $value;
	}

	private function composeUrl( array $parsed_url ) : string {
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
	 */
	public function getEndpoint() : string {
		return $this->composeUrl($this->endpointParts);
	}

	/**
	 * Sets the request URI
	 */
	public function setEndpoint( string $endpoint ) : void {
		$parts = parse_url($endpoint);
		if( $parts === false ) {
			throw new InvalidArgumentException("Failed to parse url '{$endpoint}'");
		}

		$this->endpointParts = $parts;
	}

	protected function buildCookieHeader(array $cookies) : string {
		if( function_exists('http_build_cookie') ) {
			return http_build_cookie($cookies);
		}

		$output = [];
		foreach( $cookies as $key => $value ) {
			$output[] = sprintf("%s=%s", $key, rawurlencode($value));
		}

		return implode('; ', $output);
	}

	/**
	 * Execute the request
	 *
	 * @return HttpResponseInterface An object representing the result of the request
	 */
	public function makeRequest() : HttpResponseInterface {
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
			curl_setopt($ch, CURLOPT_COOKIE, $this->buildCookieHeader($this->cookies));
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

		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers    = substr($response, 0, $headerSize);

		$body = substr($response, $headerSize);

		curl_close($ch);

		/** @var ResponseInterface[] $responses */
		$responses               = [];
		$parsedHeadersByResponse = $this->parseMultipleRequestHeaders($headers);
		foreach( $parsedHeadersByResponse as $parsedHeaders ) {

			$response = $this->responseFactory->createResponse();
			foreach( $parsedHeaders as $key => $value ) {
				if( $key === 0 ) {
					continue;
				}

				$response = $response->withAddedHeader($key, $value);
			}

			if( isset($parsedHeaders[0]) && $parts = $this->parseStartLine($parsedHeaders[0]) ) {
				[$version, $statusCode, $statusText] = $parts;

				$response = $response
					->withStatus($statusCode, $statusText)
					->withProtocolVersion($version);
			} else {
				throw new ResponseException('Unable to parse response');
			}

			$responses[] = $response;
		}

		$lastResponse = end($responses);
		if( $lastResponse === false ) {
			throw new ResponseException('Unable to parse response');
		}

		$responses[count($responses) - 1] = $lastResponse->withBody(
			$this->streamFactory->createStream($body)
		);

		return $this->responseCollectionFactory->newInstance(
			$this,
			$headers,
			...$responses
		);
	}

	/**
	 * @internal This method is public for testing purposes only
	 */
	public function parseStartLine( string $start ) : ?array {
		if( !preg_match('%^HTTP/([\d.]+)\s+(\d+)(?:\s+(.*))?$%', $start, $match) ) {
			return null;
		}

		$version    = $match[1];
		$statusCode = (int)$match[2];
		$statusText = $match[3] ?? '';

		return [ $version, $statusCode, $statusText ];
	}

	/**
	 * @return array<int, array<int|string, string>>
	 * @internal This method is public for testing purposes only
	 */
	public function parseMultipleRequestHeaders( string $rawHeaders ) : array {
		$output = [];

		$perResponseHeaders = explode("\r\n\r\n", trim($rawHeaders));
		foreach( $perResponseHeaders as $responseHeaders ) {
			$parsedResponseHeaders = $this->parseRequestHeaders($responseHeaders);

			$output[] = $parsedResponseHeaders;
		}

		return $output;
	}

	/**
	 * @return string[]
	 */
	private function parseRequestHeaders( string $rawHeaders ) : array {
		$headers = [];
		$key     = '';

		foreach( explode("\n", $rawHeaders) as $h ) {
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
			} elseif( strpos($h[0], "\t") === 0 ) {
				$headers[$key] .= "\r\n\t" . trim($h[0]);
			} elseif( !$key ) {
				$headers[0] = trim($h[0]);
			}
		}

		return array_change_key_case($headers);
	}

	private function detectAccept( string $endpoint ) : string {
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
	 */
	private function getFlatHeaders() : array {
		$output = [];
		foreach( $this->getHeaders() as $key => $value ) {
			$output[] = "$key: $value";
		}

		return $output;
	}

	/**
	 * @return array|false|null
	 */
	public function getCurlInfo() {
		return $this->curlInfo;
	}

	/**
	 * Get the time the last request took in seconds a float
	 *
	 * @return float|null null if there is no last request
	 */
	public function getLastRequestTime() : ?float {
		return $this->lastRequestTime;
	}

	/**
	 * Get the current maximum number of redirects(hops) a request should follow.
	 */
	public function getMaxRedirects() : int {
		return $this->maxRedirects;
	}

	/**
	 * Set the maximum number of redirects(hops) a request should follow.
	 */
	public function setMaxRedirects( int $maxRedirects ) : void {
		$this->maxRedirects = $maxRedirects;
	}

}
