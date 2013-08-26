<?php

namespace Boomerang;

$req      = new Request('http://httpbin.org/get');
$response = $req->makeRequest();

$valid = new ResponseValidator($response);

$valid->expectStatus(302, 0)
	->expectHeader('Content-Type', 'application/xml');

Boomerang::addValidator($valid);