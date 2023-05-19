<?php

use Boomerang\Boomerang;
use Boomerang\HttpResponseValidator;
use Boomerang\HttpRequest;

$req      = new HttpRequest('https://httpbin.org/get');
$response = $req->makeRequest();

$valid = new HttpResponseValidator($response);
$valid->expectStatus(200)
	->expectHeader('Content-Type', 'application/json');

Boomerang::addValidator($valid);
