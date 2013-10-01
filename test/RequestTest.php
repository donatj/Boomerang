<?php

namespace Boomerang\Test;

use Boomerang\HttpRequest;

class RequestTest extends \PHPUnit_Framework_TestCase {

	public function testAcceptOverride() {
		$req       = new HttpRequest('http://example.com/test.json');
		$headers   = $req->getHeaders();
		$headers_a = explode(",", $headers['Accept']);

		$this->assertEquals('application/json', $headers_a[0]);


		$req       = new HttpRequest('http://example.com/test.xml');
		$headers   = $req->getHeaders();
		$headers_a = explode(",", $headers['Accept']);

		$this->assertEquals('application/xml', $headers_a[0]);


		$req = new HttpRequest('http://example.com/test.xml');
		$req->setHeader('Accept', 'application/json');
		$headers   = $req->getHeaders();
		$headers_a = explode(",", $headers['Accept']);

		$this->assertEquals('application/json', $headers_a[0]);
	}

}