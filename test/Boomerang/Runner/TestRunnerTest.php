<?php

namespace Boomerang\Runner\Test;

use Boomerang\Runner\TestRunner;
use PHPUnit\Framework\TestCase;

class TestRunnerTest extends TestCase {

	public function testRunsEveryProvidedPath() {
		$firstPath  = $this->createSpecFile();
		$secondPath = $this->createSpecFile();
		$executed   = [];

		try {
			$runner = new TestRunner([ $firstPath, $secondPath ], null);
			$runner->runTests(function ( $path ) use ( &$executed ) {
				$executed[] = $path;
			});

			$this->assertSame([ $firstPath, $secondPath ], $executed);
		} finally {
			unlink($firstPath);
			unlink($secondPath);
		}
	}

	private function createSpecFile() : string {
		$path = tempnam(sys_get_temp_dir(), 'boomerang-');

		if( $path === false ) {
			$this->fail('Failed to create temporary spec file');
		}

		file_put_contents($path, "<?php\n");

		return $path;
	}

}
