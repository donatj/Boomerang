<?php

namespace Boomerang\Test;

use Boomerang\Factories\HttpResponseFactory;
use Boomerang\HttpRequest;
use Boomerang\Interfaces\HttpResponseInterface;
use donatj\MockWebServer\MockWebServer;
use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase {

	/** @var MockWebServer */
	public static $webServer;

	public static function setUpBeforeClass() {
		self::$webServer = new MockWebServer;
		self::$webServer->start();
	}

	public function testAcceptOverride() {
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

	public function testUrlParams() {
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

		$mockResponse = $this->getMockBuilder(HttpResponseInterface::class)->getMock();

		foreach( $methods as $method ) {
			$mockFactory     = $this->getMockBuilder(HttpResponseFactory::class)->getMock();
			$mockNewInstance = $mockFactory->method('newInstance');

			$mockNewInstance->will($this->returnCallback(function ( $body, $headers, HttpRequest $request ) use ( $mockResponse, $method ) {
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
		$mockResponse    = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mockFactory     = $this->getMockBuilder(HttpResponseFactory::class)->getMock();
		$mockNewInstance = $mockFactory->method('newInstance');

		$mockNewInstance->will($this->returnCallback(function ( $body, $headers, HttpRequest $request ) use ( $mockResponse ) {
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

	public function testCookiesFollowRedirects() {
		$mockResponse    = $this->getMockBuilder(HttpResponseInterface::class)->getMock();
		$mockFactory     = $this->getMockBuilder(HttpResponseFactory::class)->getMock();
		$mockNewInstance = $mockFactory->method('newInstance');

		$mockNewInstance->will($this->returnCallback(function ( $body, $headers, HttpRequest $request ) use ( $mockResponse ) {
			$data = json_decode($body, true);
			$this->assertSame($data['_COOKIE'], [
				'sessionid' => '38afes7a8',
			]);

			$this->assertSame($data['REQUEST_URI'], '/sauce');
			$headerParts = explode("\r\n\r\n", trim($headers));
			$this->assertCount(2, $headerParts);

			$this->assertStringStartsWith("HTTP/1.1 301 Moved Permanently\r\n", $headerParts[0]);
			$this->assertContains("\r\nSet-Cookie: sessionid=38afes7a8; Path=/\r\n", $headerParts[0]);

			$this->assertStringStartsWith("HTTP/1.1 200 OK\r\n", $headerParts[1]);
			$this->assertNotContains("\r\nSet-Cookie: \r\n", $headerParts[1]);

			return $mockResponse;
		}));

		$endpoint = self::$webServer->getUrlOfResponse(
			'redirecting to: /sauce',
			[
				'Set-Cookie: sessionid=38afes7a8; Path=/',
				'Location: /sauce',
			],
			301
		);

		$req = new HttpRequest($endpoint, $mockFactory);
		$req->setMaxRedirects(2);
		$req->setCookiesFollowRedirects(true);

		$resp = $req->makeRequest();
		$this->assertSame($mockResponse, $resp);
	}
}
