<?php

namespace Boomerang\Factories;

use Boomerang\HttpRequest;
use Boomerang\HttpResponse;

class HttpResponseFactory {

	/**
	 * @param string                      $body
	 * @param string                      $headers
	 * @return \Boomerang\HttpResponse
	 */
	public function newInstance( $body, $headers, HttpRequest $request = null ) {
		return new HttpResponse($body, $headers, $request);
	}

}
