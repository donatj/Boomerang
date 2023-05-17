<?php

namespace Boomerang\Interfaces;

use Boomerang\HttpRequest;

interface HttpResponseInterface extends ResponseInterface {

	/**
	 * @return string|string[]|null
	 * @todo Fix this ridiculous return type
	 */
	public function getHeader( string $header, ?int $hop = null );

	public function getHeaders( ?int $hop = null ) : ?array;

	public function getAllHeaders() : array;

	public function getRawHeaders() : string;

	public function getRequest() : ?HttpRequest;

	public function getHopCount() : int;

	public function getStatus( ?int $hop = null ) : ?int;

}
