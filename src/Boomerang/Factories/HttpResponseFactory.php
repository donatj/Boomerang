<?php

namespace Boomerang\Factories;

use Boomerang\HttpRequest;
use Boomerang\HttpResponse;

class HttpResponseFactory {

	/**
	 * @param string $body
	 * @param string $headers
	 */
	function newInstance( $body, $headers, HttpRequest $request = null ) {
		return new HttpResponse($body, $headers, $request);
	}

}