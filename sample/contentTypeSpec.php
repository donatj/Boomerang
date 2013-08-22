<?php

$req      = new Boomerang\Request('http://httpbin.org/get');
$response = $req->makeRequest();

$response->expectStatus(302, 0)
         ->expectHeader('Content-Type', 'application/json');
