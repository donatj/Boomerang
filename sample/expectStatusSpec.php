<?php

namespace Boomerang;

$req      = new Request('http://httpbin.org/status/418');
$response = $req->makeRequest();

$valid = new ResponseValidator($response);
$valid->expectStatus(418);

Boomerang::addValidator($valid);
