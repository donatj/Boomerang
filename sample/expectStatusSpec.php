<?php

namespace Boomerang;

$req      = new HttpRequest('http://httpbin.org/status/418');
$response = $req->makeRequest();

$valid = new HttpResponseValidator($response);
$valid->expectStatus(418);

Boomerang::addValidator($valid);
