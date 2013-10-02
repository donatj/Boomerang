<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\TypeExpectations\NullEx;

class NullExTest extends \PHPUnit_Framework_TestCase {

	public function testBasicMatching() {

		$x = new NullEx();

		$this->assertEquals(false, $x->match(false));
		$this->assertEquals(false, $x->match(true));
		$this->assertEquals(true, $x->match(null));
		$this->assertEquals(false, $x->match('abc'));
		$this->assertEquals(false, $x->match('23'));
		$this->assertEquals(false, $x->match(23));
		$this->assertEquals(false, $x->match('23.5'));
		$this->assertEquals(false, $x->match(23.5));
		$this->assertEquals(false, $x->match(''));
		$this->assertEquals(false, $x->match(' '));
		$this->assertEquals(false, $x->match('0'));
		$this->assertEquals(false, $x->match(0));

	}

}