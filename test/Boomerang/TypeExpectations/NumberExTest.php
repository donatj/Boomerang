<?php

namespace Boomerang\TypeExpectations\Test;

use Boomerang\TypeExpectations\NumberEx;
use PHPUnit\Framework\TestCase;

class NumberExTest extends TestCase {

	public function testBasicMatching() {

		$x = new NumberEx();
		$this->assertEquals(true, $x->match(1));
		$this->assertEquals(true, $x->match(1.0));
		$this->assertEquals(true, $x->match(1000));
		$this->assertEquals(true, $x->match(-1000));

		$this->assertEquals(true, $x->match(1.1));
		$this->assertEquals(false, $x->match([ 1 ]));
		$this->assertEquals(false, $x->match("1"));
		$this->assertEquals(false, $x->match(true));
	}

	public function testRangeMatching() {

		$x = new NumberEx(-10, 10);
		$this->assertEquals(true, $x->match(1));
		$this->assertEquals(true, $x->match(1.0));
		$this->assertEquals(true, $x->match(-1));
		$this->assertEquals(true, $x->match(-1.0));
		$this->assertEquals(true, $x->match(10));
		$this->assertEquals(true, $x->match(-10));
		$this->assertEquals(true, $x->match(10.0));
		$this->assertEquals(true, $x->match(-10.0));

		$this->assertEquals(true, $x->match(9.9));
		$this->assertEquals(true, $x->match(-9.9));
		$this->assertEquals(false, $x->match(11));
		$this->assertEquals(false, $x->match(-11));
		$this->assertEquals(false, $x->match(1000));
		$this->assertEquals(false, $x->match(-1000));

		//		$this->assertEquals(false, $x->match(9.9999999999999999999999));  this one is troublesome
		$this->assertEquals(false, $x->match([ 1 ]));
		$this->assertEquals(false, $x->match("1"));
		$this->assertEquals(false, $x->match("-1"));
		$this->assertEquals(false, $x->match(false));
	}

}
