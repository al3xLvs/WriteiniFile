<?php

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    // The code only uses PHP 8.2 syntax, so running the fixer on a newer PHP than it officially supports is safe.
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
        'declare_strict_types' => true,
        'concat_space' => ['spacing' => 'none'],
        'no_unused_imports' => true,
        'ordered_imports' => true,
    ])
    ->setFinder($finder);
