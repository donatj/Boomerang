<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\TypeExpectations\StringEx;
use PHPUnit\Framework\TestCase;

class StringExTest extends TestCase {

	public function testBasicMatching() : void {

		$x = new StringEx;

		$this->assertFalse($x->match(false));
		$this->assertFalse($x->match(true));
		$this->assertFalse($x->match(null));
		$this->assertTrue($x->match('abc'));
		$this->assertTrue($x->match('23'));
		$this->assertFalse($x->match(23));
		$this->assertTrue($x->match('23.5'));
		$this->assertFalse($x->match(23.5));
		$this->assertTrue($x->match(''));
		$this->assertTrue($x->match(' '));
		$this->assertTrue($x->match('0'));
		$this->assertFalse($x->match(0));
	}

}
