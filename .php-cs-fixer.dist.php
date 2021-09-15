<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('vendor')
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
	->setRules([
		'@PSR12' => true,
		'no_unused_imports' => true,
		'unary_operator_spaces' => true,
    ])
    ->setFinder($finder);
