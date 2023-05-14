<?php

namespace Tests\Boomerang;

use Boomerang\Factories\HttpResponseFactory;
use Boomerang\HttpRequest;
use Boomerang\HttpResponse;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase {

	protected static MockWebServer $server;

	public static function setUpBeforeClass() : void {
		self::$server = new MockWebServer;
		self::$server->start();
	}

	public function testAcceptOverride() : void {
		$req       = new HttpRequest('http://example.com/test.json');
		$headers   = $req->getHeaders();
		$headers_a = explode(',', $headers['Accept']);

		$this->assertEquals('application/json', $headers_a[0]);

		$req       = new HttpRequest('http://example.com/test.xml');
		$headers   = $req->getHeaders();
		$headers_a = explode(',', $headers['Accept']);

		$this->assertEquals('application/xml', $headers_a[0]);

		$req = new HttpRequest('http://example.com/test.xml');
		$req->setHeader('Accept', 'application/json');
		$headers   = $req->getHeaders();
		$headers_a = explode(',', $headers['Accept']);

		$this->assertEquals('application/json', $headers_a[0]);
	}

	public function testUrlParams() : void {
		$req = new HttpRequest('http://example.com/test.json?myParam=1&param2[]=1&param2[]=2');

		$this->assertSame([
			'myParam' => '1',
			'param2'  => [ '1', '2', ],
		], $req->getUrlParams());

		$this->assertSame('1', $req->getUrlParam('myParam'));
		$this->assertSame([ '1', '2', ], $req->getUrlParam('param2'));

		$req->setUrlParam('myParam', [ 1, 2, 3 ]);
		$this->assertSame([
			'myParam' => [ '1', '2', '3', ],
			'param2'  => [ '1', '2', ],
		], $req->getUrlParams());

		$req->setUrlParams([ 'sauce' => 'dog' ]);
		$this->assertSame([ 'sauce' => 'dog' ], $req->getUrlParams());
	}

	public function testMethods() : void {
		$methods = [
			HttpRequest::GET,
			HttpRequest::POST,
			HttpRequest::PUT,
			HttpRequest::PATCH,
			HttpRequest::DELETE,
			HttpRequest::TRACE,
			HttpRequest::OPTIONS,
		];

		// , [], [], '', false
		$mockResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();

		foreach( $methods as $method ) {
			$mockFactory     = $this->getMockBuilder(HttpResponseFactory::class)->getMock();
			$mockNewInstance = $mockFactory->method('newInstance');

			$mockNewInstance->willReturnCallback(function (
				string $body,
				$headers,
				HttpRequest $request
			) use ( $mockResponse, $method ) {
				$data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
				$this->assertSame($data['METHOD'], $method);
				$this->assertSame($data['INPUT'], 'This is the requests body');

				$this->assertSame($data['REQUEST_URI'], '/path?param1=1&param2=2');

				$this->assertSame([
					'param1' => '1',
					'param2' => '2',
				], $data['_GET']);

				return $mockResponse;
			});

			$req = new HttpRequest(self::$server->getServerRoot() . '/path?param1=1&param2=2', $mockFactory);
			$req->setMethod($method);
			$req->setBody('This is the requests body');
			$resp = $req->makeRequest();
			$this->assertSame($mockResponse, $resp);
		}
	}

	public function testCookiesFollowRedirects() : void {
		$mockResponse    = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$mockFactory     = $this->getMockBuilder(HttpResponseFactory::class)->getMock();
		$mockNewInstance = $mockFactory->method('newInstance');

		$mockNewInstance->willReturnCallback(function ( string $body, string $headers, HttpRequest $request ) use ( $mockResponse ) {
			$data = json_decode($body, true);
			$this->assertSame($data['_COOKIE'], [
				'sessionid' => '38afes7a8',
			]);

			$this->assertSame($data['REQUEST_URI'], '/sauce');
			$headerParts = explode("\r\n\r\n", trim($headers));
			$this->assertCount(2, $headerParts);

			$this->assertStringStartsWith("HTTP/1.1 301 Moved Permanently\r\n", $headerParts[0]);
			$this->assertNotFalse(stripos($headerParts[0], "\r\nSet-Cookie: sessionid=38afes7a8; Path=/\r\n"));

			$this->assertStringStartsWith("HTTP/1.1 200 OK\r\n", $headerParts[1]);
			$this->assertFalse(stripos($headerParts[1], "\r\nSet-Cookie: \r\n"));

			return $mockResponse;
		});

		$endpoint = self::$server->getUrlOfResponse(
			new Response(
				'redirecting to: /sauce',
				[
					'Set-Cookie: sessionid=38afes7a8; Path=/',
					'Location: /sauce',
				],
				301
			)
		);

		$req = new HttpRequest($endpoint, $mockFactory);
		$req->setMaxRedirects(2);
		$req->setCookiesFollowRedirects(true);

		$resp = $req->makeRequest();
		$this->assertSame($mockResponse, $resp);
	}

}
