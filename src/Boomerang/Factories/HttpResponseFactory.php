<?php

namespace Boomerang\Factories;

use Boomerang\HttpRequest;
use Psr\Http\Message\ResponseInterface;

class HttpResponseFactory {

	public function newInstance(
		HttpRequest $request,
		string $rawHeaders,
		ResponseInterface ...$responses
	) : \Boomerang\HttpResponse {
		return new \Boomerang\HttpResponse($request, $rawHeaders, ...$responses);
	}

}
