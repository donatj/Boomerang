<?php

namespace Boomerang;

$req      = new Request('http://httpbin.org/get');
$response = $req->makeRequest();

$valid = new ResponseValidator($response);
$valid->expectStatus(200)
	  ->expectHeaderContains('Content-Type', 'json');

Boomerang::addValidator($valid);
