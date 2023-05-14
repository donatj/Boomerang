<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\TypeExpectations\NullEx;
use PHPUnit\Framework\TestCase;

class NullExTest extends TestCase {

	public function testBasicMatching() : void {

		$x = new NullEx;

		$this->assertFalse($x->match(false));
		$this->assertFalse($x->match(true));
		$this->assertTrue($x->match(null));
		$this->assertFalse($x->match('abc'));
		$this->assertFalse($x->match('23'));
		$this->assertFalse($x->match(23));
		$this->assertFalse($x->match('23.5'));
		$this->assertFalse($x->match(23.5));
		$this->assertFalse($x->match(''));
		$this->assertFalse($x->match(' '));
		$this->assertFalse($x->match('0'));
		$this->assertFalse($x->match(0));
	}

}
