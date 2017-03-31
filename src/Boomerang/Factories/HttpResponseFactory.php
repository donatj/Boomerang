<?php

namespace Boomerang\Factories;

use Boomerang\HttpRequest;
use Boomerang\HttpResponse;

class HttpResponseFactory {

	/**
	 * @param string                      $body
	 * @param string                      $headers
	 * @param \Boomerang\HttpRequest|null $request
	 * @return \Boomerang\HttpResponse
	 */
	function newInstance( $body, $headers, HttpRequest $request = null ) {
		return new HttpResponse($body, $headers, $request);
	}

}