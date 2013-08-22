<?php

namespace Boomerang\Test;

use Boomerang\Request;

class RequestTest extends \PHPUnit_Framework_TestCase {

	public function testAcceptOverride() {
		$req       = new Request('http://example.com/test.json');
		$headers   = $req->getHeaders();
		$headers_a = explode(",", $headers['Accept']);

		$this->assertEquals($headers_a[0], 'application/json');


		$req       = new Request('http://example.com/test.xml');
		$headers   = $req->getHeaders();
		$headers_a = explode(",", $headers['Accept']);

		$this->assertEquals($headers_a[0], 'application/xml');


		$req = new Request('http://example.com/test.xml');
		$req->setHeader('Accept', 'application/json');
		$headers   = $req->getHeaders();
		$headers_a = explode(",", $headers['Accept']);

		$this->assertEquals($headers_a[0], 'application/json');
	}

}