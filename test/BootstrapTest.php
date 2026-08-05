<?php

namespace Boomerang\Test;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase {

	public function testIncludeIfExistsReturnsNullForUnexpectedIncludeResult() {
		$file = tempnam(sys_get_temp_dir(), 'boomerang-');

		if( $file === false ) {
			$this->fail('Failed to create temporary include file');
		}

		file_put_contents($file, "<?php\n\nreturn true;\n");

		try {
			$this->assertNull(\includeIfExists($file));
		} finally {
			unlink($file);
		}
	}

}
