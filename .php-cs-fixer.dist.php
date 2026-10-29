<?php

use Corpus\PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
	->files()
	->in(__DIR__ . '/src')
	->in(__DIR__ . '/test')
	->name('*.php');

$config = Config::php7x4();

return $config
	->setRules(array_merge(
		// Rule sets are applied in order, so this goes first to let the shared rules take precedence.
		[ '@PHPUnit9x1Migration:risky' => true ],
		$config->getRules(),
		[
			'no_superfluous_phpdoc_tags' => [
				'allow_mixed'       => true, // PHP 7.4 has no native `mixed` type, so these tags are the only type information
				'remove_inheritdoc' => true,
			],
		]
	))
	->setFinder($finder);
