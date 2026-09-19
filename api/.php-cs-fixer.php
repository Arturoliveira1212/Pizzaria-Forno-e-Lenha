<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS3.0' => true,
        'braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
        ],
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public', 'constant_protected', 'constant_private',
                'property_public', 'property_protected', 'property_private',
                'construct', 'destruct',
                'method',
            ],
        ],
        'class_attributes_separation' => [
            'elements' => ['method' => 'one', 'const' => 'none', 'property' => 'none',],
        ],
        'blank_line_before_statement' => [
            'statements' => ['return'],
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'trim_array_spaces' => true,
        'single_quote' => true,
        'explicit_string_variable' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'no_empty_phpdoc' => true,
    ])
    ->setFinder($finder);