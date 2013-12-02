<?php

namespace Boomerang\Factories;

use Boomerang\HttpRequest;
use Boomerang\HttpResponse;

class HttpResponseFactory {

	function newInstance( $body, $headers, HttpRequest $request = null ) {
		return new HttpResponse($body, $headers, $request);
	}

}