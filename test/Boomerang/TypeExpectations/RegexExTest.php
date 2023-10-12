<?php

namespace Tests\Boomerang\TypeExpectations;

use Boomerang\TypeExpectations\RegexEx;
use PHPUnit\Framework\TestCase;

class RegexExTest extends TestCase {

	public function testBasicMatching() : void {
		$x = new RegexEx('/.*/');

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

		$x = new RegexEx('/a/i');

		$this->assertFalse($x->match('b'));
		$this->assertFalse($x->match('bob'));
		$this->assertFalse($x->match('Robert'));
		$this->assertTrue($x->match('a'));
		$this->assertTrue($x->match('A'));
		$this->assertTrue($x->match('Albert'));
	}

}
