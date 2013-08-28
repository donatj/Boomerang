<?php

namespace Boomerang;

$req      = new Request('http://httpbin.org/get');
$response = $req->makeRequest();

$valid = new ResponseValidator($response);
$valid->expectStatus(200)
	  ->expectHeader('Content-Type', 'application/json');

Boomerang::addValidator($valid);
