<?php

namespace Boomerang\Test;

use Boomerang\HttpResponse;
use PHPUnit\Framework\TestCase;

class HttpResponseTest extends TestCase {

	public function testGetRawHeaders() {
		$headers  = <<<'EOT'
HTTP/1.1 200 OK
Content-Encoding:gzip
Content-language:en



EOT;
		$response = new HttpResponse('', $headers);

		$this->assertEquals($headers, $response->getRawHeaders());
		$this->assertSame(1, $response->getHopCount());
	}

	//@todo add just \r's hard, and \r\n's hard

	public function testBasicHeaderParsing() {
		$response = new HttpResponse('', <<<'EOT'
HTTP/1.1 200 OK
Cache-Control:no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Connection:close
Content-Encoding:gzip
Content-language:en
Content-Length:30845
Content-Type:text/html; charset=utf-8
Date:Mon, 26 Aug 2013 21:51:41 GMT
Expires:Thu, 19 Nov 1981 08:52:00 GMT
Pragma:no-cache
Server:Apache/2.2.21 (FreeBSD) mod_ssl/2.2.21 OpenSSL/0.9.8q PHP/5.4.16-dev
Vary:User-Agent,Accept-Encoding
X-Powered-By:PHP/5.4.16-dev
EOT
		);

		$headers_a = $response->getHeaders();

		$this->assertEquals($headers_a[0], 'HTTP/1.1 200 OK');
		$this->assertEquals(200, $response->getStatus());
		$this->assertEquals('no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
			$response->getHeader('Cache-Control'));
		$this->assertEquals('close',
			$response->getHeader('Connection'));
		$this->assertEquals('gzip',
			$response->getHeader('Content-Encoding'));
		$this->assertEquals('en',
			$response->getHeader('Content-language'));
		$this->assertEquals('30845',
			$response->getHeader('Content-Length'));
		$this->assertEquals('text/html; charset=utf-8',
			$response->getHeader('Content-Type'));
		$this->assertEquals('Mon, 26 Aug 2013 21:51:41 GMT',
			$response->getHeader('Date'));
		$this->assertEquals('Thu, 19 Nov 1981 08:52:00 GMT',
			$response->getHeader('Expires'));
		$this->assertEquals('no-cache',
			$response->getHeader('Pragma'));
		$this->assertEquals('Apache/2.2.21 (FreeBSD) mod_ssl/2.2.21 OpenSSL/0.9.8q PHP/5.4.16-dev',
			$response->getHeader('Server'));
		$this->assertEquals('User-Agent,Accept-Encoding',
			$response->getHeader('Vary'));
		$this->assertEquals('PHP/5.4.16-dev',
			$response->getHeader('X-Powered-By'));

		$this->assertNull($response->getHeader('RandomFakeNotSetHeader', 0));

		//Sometimes you can get trailing newlines. Make sure this doesn't break things
		$response = new HttpResponse("", <<<'EOT'
HTTP/1.1 200 OK
Content-Encoding:gzip
Content-language:en



EOT
		);

		$headers_a = $response->getHeaders();

		$this->assertEquals($headers_a[0], 'HTTP/1.1 200 OK');
		$this->assertEquals('gzip',
			$response->getHeader('Content-Encoding'));
		$this->assertEquals('en',
			$response->getHeader('Content-language'));

		$this->assertEquals(null,
			$response->getHeader('RandomFakeNotSetHeader', 0));

		$this->assertSame(1, $response->getHopCount());
	}

	public function testMultihopHeaderParsing() {
		$response = new HttpResponse("", <<<'EOT'
HTTP/1.1 302 Found
Date: Mon, 26 Aug 2013 22:13:30 GMT
Server: Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Set-Cookie: anchor_node_id=2; expires=Tue, 27-Aug-2013 22:13:30 GMT; path=/
Location: http://local.myon.com/books/categories.html
Content-Length: 0
Content-Type: application/json

HTTP/1.1 200 OK
Date: Mon, 26 Aug 2013 22:13:30 GMT
Server: Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Transfer-Encoding: chunked
Content-Type: text/html; charset=utf-8

EOT
		);

		//first hop
		$headers_a0 = $response->getHeaders(0);

		$this->assertEquals('HTTP/1.1 302 Found', $headers_a0[0]);
		$this->assertEquals(302, $response->getStatus(0));
		$this->assertEquals('Mon, 26 Aug 2013 22:13:30 GMT',
			$response->getHeader('Date', 0));
		$this->assertEquals('Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x',
			$response->getHeader('Server', 0));
		$this->assertEquals('Thu, 19 Nov 1981 08:52:00 GMT',
			$response->getHeader('Expires', 0));
		$this->assertEquals('no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
			$response->getHeader('Cache-Control', 0));
		$this->assertEquals('no-cache',
			$response->getHeader('Pragma', 0));
		$this->assertEquals('anchor_node_id=2; expires=Tue, 27-Aug-2013 22:13:30 GMT; path=/',
			$response->getHeader('Set-Cookie', 0));
		$this->assertEquals('http://local.myon.com/books/categories.html',
			$response->getHeader('Location', 0));
		$this->assertEquals('0',
			$response->getHeader('Content-Length', 0));
		$this->assertEquals('application/json',
			$response->getHeader('Content-Type', 0));

		$this->assertEquals(null,
			$response->getHeader('RandomFakeNotSetHeader', 0));

		//second hop
		$headers_a1 = $response->getHeaders(1);

		$this->assertEquals('HTTP/1.1 200 OK', $headers_a1[0]);
		$this->assertEquals(200, $response->getStatus(1));

		$this->assertEquals('Mon, 26 Aug 2013 22:13:30 GMT',
			$response->getHeader('Date', 1));
		$this->assertEquals('Apache/2.2.22 (Unix) DAV/2 PHP/5.3.15 with Suhosin-Patch mod_ssl/2.2.22 OpenSSL/0.9.8x',
			$response->getHeader('Server', 1));
		$this->assertEquals('Thu, 19 Nov 1981 08:52:00 GMT',
			$response->getHeader('Expires', 1));
		$this->assertEquals('no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
			$response->getHeader('Cache-Control', 1));
		$this->assertEquals('no-cache',
			$response->getHeader('Pragma', 1));
		$this->assertEquals('chunked',
			$response->getHeader('Transfer-Encoding', 1));
		$this->assertEquals('text/html; charset=utf-8',
			$response->getHeader('Content-Type', 1));

		$this->assertEquals(null,
			$response->getHeader('RandomFakeNotSetHeader', 1));

		//non-existent third hop
		$this->assertNull($response->getHeader(3));

		$this->assertSame(2, $response->getHopCount());
	}

	public function testDuplicativeHeaders() {
		$body     = "This is my test body";
		$response = new HttpResponse($body, <<<'EOT'
HTTP/1.1 200 OK
Cache-Control: no-cache
Cache-Control: no-store

HTTP/1.1 500 OK
Cache-Control: no-cache 
Cache-Control: no-store
Cache-Control: no-transform
Cache-Control: only-if-cached
EOT
		);

		$this->assertSame([
			[
				'HTTP/1.1 200 OK',
				'cache-control' => [
					'no-cache', 'no-store',
				],
			],
			[
				'HTTP/1.1 500 OK',
				'cache-control' => [
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

		$this->assertSame([
			'no-cache', 'no-store',
		], $response->getHeader('Cache-Control', 0));

		$this->assertSame([
			'no-cache',
			'no-store',
			'no-transform',
			'only-if-cached',
		], $response->getHeader('Cache-Control', 1));

		$this->assertNull($response->getHeader('Cache-Control', 2));

		$this->assertSame(500, $response->getStatus());
		$this->assertSame(200, $response->getStatus(0));
		$this->assertSame(500, $response->getStatus(1));
		$this->assertNull($response->getStatus(2));

		$this->assertSame($body, $response->getBody());
		$this->assertSame(2, $response->getHopCount());
	}

	public function testMissingStatus() {
		$response = new HttpResponse("", <<<'EOT'
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
EOT
		);

		$this->assertNull($response->getStatus());
	}

}