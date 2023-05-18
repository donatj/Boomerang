<?php

namespace Tests\Boomerang;

use Boomerang\HttpResponse;
use donatj\MockWebServer\MockWebServer;
use Http\Discovery\Psr17Factory;
use PHPUnit\Framework\TestCase;

class HttpResponseTest extends TestCase {

	private static MockWebServer $server;

	public static function setUpBeforeClass() : void {
		self::$server = new MockWebServer;
		self::$server->start();
	}

	public static function tearDownAfterClass() : void {
		self::$server->stop();
	}

	protected function setUp() : void {
		parent::setUp();

		$this->request = $this->getMockBuilder(\Boomerang\HttpRequest::class)->disableOriginalConstructor()->getMock();
	}

	public function testGetRawHeaders() : void {
		$headers  = <<<'EOT'
			HTTP/1.1 200 OK
			Content-Encoding:gzip
			Content-language:en



			EOT;
		$response = new HttpResponse($this->request, $headers);

		$this->assertEquals($headers, $response->getRawHeaders());
	}

	// @todo add just \r's hard, and \r\n's hard

	public function testBasicHeaderHandling() : void {
		$serverResponse = (new Psr17Factory)
			->createResponse(200, 'OK')
			->withProtocolVersion('1.1')
			->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
			->withHeader('Connection', 'close')
			->withHeader('Content-Encoding', 'gzip')
			->withHeader('Content-language', 'en')
			->withHeader('Content-Length', '30845')
			->withHeader('Content-Type', 'text/html; charset=utf-8')
			->withHeader('Date', 'Mon, 26 Aug 2013 21:51:41 GMT')
			->withHeader('Expires', 'Thu, 19 Nov 1981 08:52:00 GMT')
			->withHeader('Pragma', 'no-cache')
			->withHeader('Server', 'Apache/2.2.21 (FreeBSD) mod_ssl/2.2.21 OpenSSL/0.9.8q PHP/5.4.16-dev')
			->withHeader('Vary', 'User-Agent,Accept-Encoding')
			->withHeader('X-Powered-By', 'PHP/5.4.16-dev');

		$response = new HttpResponse($this->request, '', $serverResponse);

		$this->assertEquals('1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatus());
		$this->assertEquals([ 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0' ],
			$response->getHeader('Cache-Control'));
		$this->assertEquals([ 'close' ],
			$response->getHeader('Connection'));
		$this->assertEquals([ 'gzip' ],
			$response->getHeader('Content-Encoding'));
		$this->assertEquals([ 'en' ],
			$response->getHeader('Content-language'));
		$this->assertEquals([ '30845' ],
			$response->getHeader('Content-Length'));
		$this->assertEquals([ 'text/html; charset=utf-8' ],
			$response->getHeader('Content-Type'));
		$this->assertEquals([ 'Mon, 26 Aug 2013 21:51:41 GMT' ],
			$response->getHeader('Date'));
		$this->assertEquals([ 'Thu, 19 Nov 1981 08:52:00 GMT' ],
			$response->getHeader('Expires'));
		$this->assertEquals([ 'no-cache' ],
			$response->getHeader('Pragma'));
		$this->assertEquals([ 'Apache/2.2.21 (FreeBSD) mod_ssl/2.2.21 OpenSSL/0.9.8q PHP/5.4.16-dev' ],
			$response->getHeader('Server'));
		$this->assertEquals([ 'User-Agent,Accept-Encoding' ],
			$response->getHeader('Vary'));
		$this->assertEquals([ 'PHP/5.4.16-dev' ],
			$response->getHeader('X-Powered-By'));

		$this->assertEquals([], $response->getHeader('RandomFakeNotSetHeader'));
	}

	public function testMultihopHeaderHandling() : void {
		$serverResponse1 = (new Psr17Factory)
			->createResponse(302, 'Found')
			->withProtocolVersion('1.1')
			->withHeader('Date', 'Mon, 26 Aug 2013 22:13:30 GMT')
			->withHeader('Server', 'Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x')
			->withHeader('Expires', 'Thu, 19 Nov 1981 08:52:00 GMT')
			->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
			->withHeader('Pragma', 'no-cache')
			->withHeader('Set-Cookie', 'anchor_node_id=2; expires=Tue, 27-Aug-2013 22:13:30 GMT; path=/')
			->withHeader('Location', 'http://local.example.com/books/categories.html')
			->withHeader('Content-Length', '0')
			->withHeader('Content-Type', 'application/json');

		$serverResponse2 = (new Psr17Factory)
			->createResponse(200, 'OK')
			->withProtocolVersion('2')
			->withHeader('Date', 'Mon, 26 Aug 2013 22:13:31 GMT')
			->withHeader('Server', 'Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x')
			->withHeader('Expires', 'Thu, 19 Nov 1981 08:52:00 GMT')
			->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
			->withHeader('Pragma', 'no-cache')
			->withHeader('Transfer-Encoding', 'chunked')
			->withHeader('Content-Type', 'text/html; charset=utf-8');

		$response = new HttpResponse($this->request, '', $serverResponse1, $serverResponse2);

		// first hop
		$headers_a0 = $response->getHeaders(0);

		$this->assertEquals('1.1', $response->getProtocolVersion(0));
		$this->assertEquals(302, $response->getStatus(0));
		$this->assertEquals([ 'Mon, 26 Aug 2013 22:13:30 GMT' ],
			$response->getHeader('Date', 0));
		$this->assertEquals([ 'Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x' ],
			$response->getHeader('Server', 0));
		$this->assertEquals([ 'Thu, 19 Nov 1981 08:52:00 GMT' ],
			$response->getHeader('Expires', 0));
		$this->assertEquals([ 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0' ],
			$response->getHeader('Cache-Control', 0));
		$this->assertEquals([ 'no-cache' ],
			$response->getHeader('Pragma', 0));
		$this->assertEquals([ 'anchor_node_id=2; expires=Tue, 27-Aug-2013 22:13:30 GMT; path=/' ],
			$response->getHeader('Set-Cookie', 0));
		$this->assertEquals([ 'http://local.example.com/books/categories.html' ],
			$response->getHeader('Location', 0));
		$this->assertEquals([ '0' ],
			$response->getHeader('Content-Length', 0));
		$this->assertEquals([ 'application/json' ],
			$response->getHeader('Content-Type', 0));

		$this->assertEquals([],
			$response->getHeader('RandomFakeNotSetHeader', 0));

		// second hop
		$headers_a1 = $response->getHeaders(1);

		$this->assertEquals('2', $response->getProtocolVersion(1));
		$this->assertEquals(200, $response->getStatus(1));

		$this->assertEquals([ 'Mon, 26 Aug 2013 22:13:31 GMT' ],
			$response->getHeader('Date', 1));
		$this->assertEquals([ 'Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x' ],
			$response->getHeader('Server', 1));
		$this->assertEquals([ 'Thu, 19 Nov 1981 08:52:00 GMT' ],
			$response->getHeader('Expires', 1));
		$this->assertEquals([ 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0' ],
			$response->getHeader('Cache-Control', 1));
		$this->assertEquals([ 'no-cache' ],
			$response->getHeader('Pragma', 1));
		$this->assertEquals([ 'chunked' ],
			$response->getHeader('Transfer-Encoding', 1));
		$this->assertEquals([ 'text/html; charset=utf-8' ],
			$response->getHeader('Content-Type', 1));

		$this->assertEquals([],
			$response->getHeader('RandomFakeNotSetHeader', 1));

		// non-existent third hop
		$this->assertEquals([], $response->getHeader(3));

		$this->assertSame(2, $response->getHopCount());
	}

	public function testDuplicativeHeaders() : void {
		$body     = "This is my test body";

		$serverResponse1 = (new Psr17Factory)
			->createResponse(200, 'OK')
			->withAddedHeader('Cache-Control', 'no-cache')
			->withAddedHeader('Cache-Control', 'no-store');

		$serverResponse2 = (new Psr17Factory)
			->createResponse(500, 'OK')
			->withAddedHeader('Cache-Control', 'no-cache')
			->withAddedHeader('Cache-Control', 'no-store')
			->withAddedHeader('Cache-Control', 'no-transform')
			->withAddedHeader('Cache-Control', 'only-if-cached')
			->withBody((new Psr17Factory)->createStream($body));

		$response = new HttpResponse($this->request, '', $serverResponse1, $serverResponse2);

		$this->assertSame([
			[
				'Cache-Control' => [
					'no-cache',
					'no-store',
				],
			],
			[
				'Cache-Control' => [
					'no-cache',
					'no-store',
					'no-transform',
					'only-if-cached',
				],
			],

		], $response->getAllHeaders());

		$this->assertSame($response->getHeader('Cache-Control'), [
			'no-cache',
			'no-store',
			'no-transform',
			'only-if-cached',
		]);

		$this->assertSame($response->getHeader('cache-control'), [
			'no-cache',
			'no-store',
			'no-transform',
			'only-if-cached',
		]); // testing case insensitivity

		$this->assertSame([
			'no-cache', 'no-store',
		], $response->getHeader('Cache-Control', 0));

		$this->assertSame([
			'no-cache',
			'no-store',
			'no-transform',
			'only-if-cached',
		], $response->getHeader('Cache-Control', 1));

		$this->assertEquals([], $response->getHeader('Cache-Control', 2));

		$this->assertSame(500, $response->getStatus());
		$this->assertSame(200, $response->getStatus(0));
		$this->assertSame(500, $response->getStatus(1));
		$this->assertNull($response->getStatus(2));

		$this->assertSame($body, $response->getBody());
		$this->assertSame(2, $response->getHopCount());
	}

	public function testMissingStatus() : void {
		$response = new HttpResponse($this->request, '');

		$this->assertNull($response->getStatus());

		$response = new HttpResponse($this->request, '', (new Psr17Factory)->createResponse());
		$this->assertNotNull($response->getStatus());
		$this->assertNotNull($response->getStatus(0));
		$this->assertNull($response->getStatus(-1));
	}

}
