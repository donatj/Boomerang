<?php

namespace Boomerang\Factories;

use Boomerang\HttpRequest;
use Boomerang\HttpResponse;

class HttpResponseFactory {

	public function newInstance( string $body, string $headers, ?HttpRequest $request = null ) : HttpResponse {
		return new HttpResponse($body, $headers, $request);
	}

}
