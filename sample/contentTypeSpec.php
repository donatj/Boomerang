<?php

$req = new Phrisby\Request('http://httpbin.org/get');
$response = $req->makeRequest();

$response->expectStatus(302, 0)
         ->expectHeader('Content-Type', 'application/json');
