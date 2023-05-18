<?php

namespace Boomerang;

use Boomerang\Interfaces\HttpResponseInterface;
use Psr\Http\Message\ResponseInterface;

class HttpResponse implements HttpResponseInterface {

	private array $responses;

	private HttpRequest $request;
	private string $rawHeaders;

	public function __construct( HttpRequest $request, string $rawHeaders, ResponseInterface ...$responses ) {
		$this->request    = $request;
		$this->rawHeaders = $rawHeaders;
		$this->responses  = $responses;
	}

	protected function hop( ?int $hop ) : ?ResponseInterface {
		if( $hop === null ) {
			return end($this->responses) ?: null;
		}

		return $this->responses[$hop] ?? null;
	}

	public function getHeader( string $header, ?int $hop = null ) : array {
		$response = $this->hop($hop);
		if( $response === null ) {
			return [];
		}

		return $response->getHeader($header) ?: [];
	}

	public function getHeaders( ?int $hop = null ) : ?array {
		$response = $this->hop($hop);
		if( $response === null ) {
			return null;
		}

		return $response->getHeaders();
	}

	public function getAllHeaders() : array {
		$headers = [];
		foreach( $this->responses as $response ) {
			$headers[] = $response->getHeaders();
		}

		return $headers;
	}

	public function getRawHeaders() : string {
		return $this->rawHeaders;
	}

	public function getRequest() : ?HttpRequest {
		return $this->request;
	}

	public function getHopCount() : int {
		return count($this->responses);
	}

	public function getStatus( ?int $hop = null ) : ?int {
		$response = $this->hop($hop);
		if( $response === null ) {
			return null;
		}

		return $response->getStatusCode();
	}

	public function getProtocolVersion( ?int $hop = null ) : ?string {
		$response = $this->hop($hop);
		if( $response === null ) {
			return null;
		}

		return $response->getProtocolVersion();
	}

	public function getBody() : string {
		$response = $this->hop(null);
		if( $response === null ) {
			return '';
		}

		return $response->getBody()->getContents();
	}

}
