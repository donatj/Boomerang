<?php

namespace Boomerang\Factories;

class HttpResponseFactory {

	public function newInstance(
		\Boomerang\HttpRequest $request,
		string $rawHeaders,
		\Psr\Http\Message\ResponseInterface ...$responses
	) : \Boomerang\HttpResponse {
		return new \Boomerang\HttpResponse($request, $rawHeaders, ...$responses);
	}

}
