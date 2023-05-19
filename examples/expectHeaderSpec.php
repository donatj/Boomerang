<?php

use Boomerang\Boomerang;
use Boomerang\HttpRequest;
use Boomerang\HttpResponseValidator;

$req      = new HttpRequest('https://httpbin.org/get');
$response = $req->makeRequest();

$valid = new HttpResponseValidator($response);
$valid->expectStatus(200)
	->expectHeader('Content-Type', 'application/json');

Boomerang::addValidator($valid);
