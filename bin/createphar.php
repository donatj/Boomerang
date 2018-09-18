<?php

echo "Building Phar... ";

$path = realpath(__DIR__ . '/../') . '/';

$srcRoot = $path . "src";
$buildRoot = $path . "build";

if( isset($argv[1]) ) {
	$fpath = $argv[1];
}else{
	$fpath = $buildRoot . "/boomerang.phar";
}

$phar = new Phar($fpath, 
	FilesystemIterator::CURRENT_AS_FILEINFO |     	FilesystemIterator::KEY_AS_FILENAME, "myapp.phar");

$phar->buildFromDirectory($path,'%(src|vendor)/.*\.php$%');

$phar->setStub(file_get_contents($path . 'build/stub.php'));

chmod($fpath, 0777);

echo "Done!\007" . PHP_EOL . PHP_EOL;
