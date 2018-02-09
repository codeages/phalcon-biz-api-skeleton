<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/bootstrap')
    ->in(__DIR__.'/config')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests/ApiTest')
    ->in(__DIR__.'/tests/UnitTest')

;

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules(array(
        '@Symfony' => true,
        'phpdoc_summary' => false,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
    ))
    ->setFinder($finder)
;
