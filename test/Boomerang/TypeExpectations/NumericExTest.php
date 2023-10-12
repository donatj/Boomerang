<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\TypeExpectations\NumericEx;
use PHPUnit\Framework\TestCase;

class NumericExTest extends TestCase {

	public function testBasicMatching() : void {

		$x = new NumericEx;
		$this->assertTrue($x->match(1));
		$this->assertTrue($x->match(1.0));
		$this->assertTrue($x->match(1000));
		$this->assertTrue($x->match(-1000));

		$this->assertTrue($x->match(1.1));
		$this->assertFalse($x->match([ 1 ]));
		$this->assertTrue($x->match("1"));
		$this->assertFalse($x->match(true));
	}

	public function testRangeMatching() : void {

		$x = new NumericEx(-10, 10);
		$this->assertTrue($x->match(1));
		$this->assertTrue($x->match(1.0));
		$this->assertTrue($x->match(-1));
		$this->assertTrue($x->match(-1.0));
		$this->assertTrue($x->match(10));
		$this->assertTrue($x->match(-10));
		$this->assertTrue($x->match(10.0));
		$this->assertTrue($x->match(-10.0));

		$this->assertTrue($x->match(9.9));
		$this->assertTrue($x->match(-9.9));
		$this->assertFalse($x->match(11));
		$this->assertFalse($x->match(-11));
		$this->assertFalse($x->match(1000));
		$this->assertFalse($x->match(-1000));

		//		$this->assertEquals(false, $x->match(9.9999999999999999999999));  this one is troublesome
		$this->assertFalse($x->match([ 1 ]));
		$this->assertTrue($x->match("1"));
		$this->assertTrue($x->match("-1"));
		$this->assertFalse($x->match(false));
	}

}
