<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setIndent('    ')
    ->setRules([
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@DoctrineAnnotation' => false,
        '@PHPUnit48Migration:risky' => true,
        // part of `PHPUnitXYMigration:risky` ruleset, to be enabled when PHPUnit 4.x support will be dropped
        // as we don't want to rewrite exceptions handling twice
        'php_unit_no_expectation_annotation' => false,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'array_syntax' => ['syntax' => 'short'],
        'strict_param' => true,
        'fopen_flags' => false,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'array_indentation' => true,
        'indentation_type' => true,
        'static_lambda' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->append([__FILE__])
            ->exclude([
                'bin',
            ]),
        // ->notPath('somepath_to_a_file')
    )
;
