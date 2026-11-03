<?php

namespace Boomerang\Test;

use PHPUnit\Framework\TestCase;

class BoomerangCliTest extends TestCase {

	public function testDisplaysEachValidatorOnceWithoutDeprecations() {
		$root = dirname(__DIR__, 2);
		$command = [
			PHP_BINARY,
			'-d', 'error_reporting=-1',
			'-d', 'display_errors=stderr',
			'-d', 'log_errors=0',
			$root . '/composer/bin/boomerang',
			$root . '/test/fixtures/cli/FirstSpec.php',
			$root . '/test/fixtures/cli/SecondSpec.php',
		];
		$process = proc_open($command, [ 0 => [ 'pipe', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ], $pipes, $root);
		$this->assertIsResource($process);
		fclose($pipes[0]);
		$output = stream_get_contents($pipes[1]);
		$error = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$status = proc_close($process);

		$this->assertSame(0, $status, $error);
		$this->assertSame('', $error);
		$output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
		$this->assertSame(1, substr_count($output, "\n.\n"));
		$this->assertStringContainsString('1 tests, 0 assertions, 0 failures', $output);
	}

}
