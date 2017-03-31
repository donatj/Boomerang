<?php

namespace Boomerang\Test;

use Boomerang\HttpRequest;

class HttpRequestTest extends \PHPUnit_Framework_TestCase {

	/** @var \TestWebServer */
	public static $webServer;

	public static function setUpBeforeClass() {
		require_once(__DIR__ . '/../Server/TestWebServer.php');
		self::$webServer = new \TestWebServer();
	}

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

	public function testMethods() {
		$methods = [
			HttpRequest::GET,
			HttpRequest::POST,
			HttpRequest::PUT,
			HttpRequest::PATCH,
			HttpRequest::DELETE,
			HttpRequest::TRACE,
			HttpRequest::OPTIONS,
		];

		$mockResponse = $this->getMock('Boomerang\\HttpResponse', [], [], '', false);

		foreach( $methods as $method ) {
			$mockFactory    = $this->getMock('Boomerang\\Factories\\HttpResponseFactory');
			$mockNewInstace = $mockFactory->method('newInstance');

			$mockNewInstace->will($this->returnCallback(function ( $body, $headers, HttpRequest $request ) use ( $mockResponse, $method ) {
				$data = json_decode($body, true);
				$this->assertSame($data['METHOD'], $method);
				$this->assertSame($data['INPUT'], 'This is the requests body');

				$this->assertSame($data['REQUEST_URI'], '/path?param1=1&param2=2');

				$this->assertSame([
					'param1' => '1',
					'param2' => '2',
				], $data['_GET']);

				return $mockResponse;
			}));

			$req = new HttpRequest(self::$webServer->getServerRoot() . '/path?param1=1&param2=2', $mockFactory);
			$req->setMethod($method);
			$req->setBody('This is the requests body');
			$resp = $req->makeRequest();
			$this->assertSame($mockResponse, $resp);
		}
	}

	public function testDeprecatedPostMethod() {
		$mockResponse = $this->getMock('Boomerang\\HttpResponse', [], [], '', false);
		$mockFactory    = $this->getMock('Boomerang\\Factories\\HttpResponseFactory');
		$mockNewInstace = $mockFactory->method('newInstance');

		$mockNewInstace->will($this->returnCallback(function ( $body, $headers, HttpRequest $request ) use ( $mockResponse ) {
			$data = json_decode($body, true);
			$this->assertSame($data['METHOD'], HttpRequest::POST);
			$this->assertSame($data['INPUT'], '');
			$this->assertSame($data['_POST'], [
				'myPostKey' => 'myPostedValue',
			]);

			$this->assertSame($data['REQUEST_URI'], '/postTest');

			return $mockResponse;
		}));

		$req = new HttpRequest(self::$webServer->getServerRoot() . '/postTest', $mockFactory);
		$this->assertSame(HttpRequest::GET, $req->getMethod());
		$req->setPost('myPostKey', 'myPostedValue');
		$this->assertSame(HttpRequest::POST, $req->getMethod());

		$resp = $req->makeRequest();
		$this->assertSame($mockResponse, $resp);
	}

}