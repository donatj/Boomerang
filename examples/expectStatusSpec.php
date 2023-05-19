<?php

use Boomerang\Boomerang;
use Boomerang\HttpRequest;
use Boomerang\HttpResponseValidator;

$req      = new HttpRequest('https://httpbin.org/status/418');
$response = $req->makeRequest();

$valid = new HttpResponseValidator($response);
$valid->expectStatus(418);

Boomerang::addValidator($valid);
