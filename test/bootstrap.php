<?php

if( class_exists('\PHPUnit\Runner\Version') ) {
	require __DIR__ . '/BaseServerTest/BaseServerTest_phpunit9.php';
} else {
	require __DIR__ . '/BaseServerTest/BaseServerTest_phpunit4.php';
}

return require __DIR__ . '/../src/bootstrap.php';
