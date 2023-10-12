<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\TypeExpectations\NumericStringEx;
use PHPUnit\Framework\TestCase;

class NumericStringExTest extends TestCase {

	public function testBasicMatching() : void {

		$x = new NumericStringEx;
		$this->assertFalse($x->match(1));
		$this->assertFalse($x->match(1.0));
		$this->assertFalse($x->match(1000));
		$this->assertFalse($x->match(-1000));

		// ---

		$this->assertTrue($x->match("1"));
		$this->assertTrue($x->match("1.0"));
		$this->assertTrue($x->match("1000"));
		$this->assertTrue($x->match("-1000"));

		$this->assertFalse($x->match(1.1));
		$this->assertFalse($x->match([ 1 ]));
		$this->assertTrue($x->match("1"));
		$this->assertFalse($x->match(true));
	}

	public function testRangeMatching() : void {

		$x = new NumericStringEx(-10, 10);
		$this->assertFalse($x->match(1));
		$this->assertFalse($x->match(1.0));
		$this->assertFalse($x->match(-1));
		$this->assertFalse($x->match(-1.0));
		$this->assertFalse($x->match(10));
		$this->assertFalse($x->match(-10));
		$this->assertFalse($x->match(10.0));
		$this->assertFalse($x->match(-10.0));

		$this->assertFalse($x->match(9.9));
		$this->assertFalse($x->match(-9.9));
		$this->assertFalse($x->match(11));
		$this->assertFalse($x->match(-11));
		$this->assertFalse($x->match(1000));
		$this->assertFalse($x->match(-1000));

		// ---

		$this->assertTrue($x->match('1'));
		$this->assertTrue($x->match('1.0'));
		$this->assertTrue($x->match('-1'));
		$this->assertTrue($x->match('-1.0'));
		$this->assertTrue($x->match('10'));
		$this->assertTrue($x->match('-10'));
		$this->assertTrue($x->match('10.0'));
		$this->assertTrue($x->match('-10.0'));

		$this->assertTrue($x->match('9.9'));
		$this->assertTrue($x->match('-9.9'));
		$this->assertFalse($x->match('11'));
		$this->assertFalse($x->match('-11'));
		$this->assertFalse($x->match('1000'));
		$this->assertFalse($x->match('-1000'));

		//		$this->assertFalse($x->match(9.9999999999999999999999));  this one is troublesome
		$this->assertFalse($x->match([ 1 ]));
		$this->assertTrue($x->match("1"));
		$this->assertTrue($x->match("-1"));
		$this->assertFalse($x->match(false));
	}

}
