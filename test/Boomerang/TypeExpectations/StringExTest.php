<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\TypeExpectations\StringEx;
use PHPUnit\Framework\TestCase;

class StringExTest extends TestCase {

	public function testBasicMatching() {

		$x = new StringEx();

		$this->assertEquals(false, $x->match(false));
		$this->assertEquals(false, $x->match(true));
		$this->assertEquals(false, $x->match(null));
		$this->assertEquals(true, $x->match('abc'));
		$this->assertEquals(true, $x->match('23'));
		$this->assertEquals(false, $x->match(23));
		$this->assertEquals(true, $x->match('23.5'));
		$this->assertEquals(false, $x->match(23.5));
		$this->assertEquals(true, $x->match(''));
		$this->assertEquals(true, $x->match(' '));
		$this->assertEquals(true, $x->match('0'));
		$this->assertEquals(false, $x->match(0));
	}

}
