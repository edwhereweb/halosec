<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder);
