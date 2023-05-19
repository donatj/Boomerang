<?php

use Boomerang\Boomerang;
use Boomerang\HttpResponseValidator;
use Boomerang\HttpRequest;

$req      = new HttpRequest('https://httpbin.org/status/418');
$response = $req->makeRequest();

$valid = new HttpResponseValidator($response);
$valid->expectStatus(418);

Boomerang::addValidator($valid);
