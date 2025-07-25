<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = PhpCsFixer\Config::create();

return $config->setRules([
    '@PSR2' => true,
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => ['sortAlgorithm' => 'alpha'],
    'no_unused_imports' => true,
    'trailing_comma_in_multiline_array' => true,
    'phpdoc_scalar' => true,
    'unary_operator_spaces' => true,
    'binary_operator_spaces' => [
        'default' => 'single_space',
        'operators' => [
            '=>' => 'align_single_space_minimal',
        ],
    ],
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_var_without_name' => true,
    'class_attributes_separation' => [
        'elements' => ['method'],
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'single_trait_insert_per_statement' => true,
])
    ->setFinder($finder); 