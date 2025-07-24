<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(
        [
            'build',
            'docs',
            'wiki',
            'temp',
        ]
    );

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules(
        [
            '@PhpCsFixer'                => true,
            '@PER-CS'                    => true,
            '@PSR12'                     => true,
            '@PHP84Migration'            => true,
            'array_syntax'               => ['syntax' => 'short'],
            'align_multiline_comment'    => true,
            'array_indentation'          => true,
            'ordered_imports'            => true,
            'global_namespace_import'    => true,
            'no_unused_imports'          => true,
            'declare_strict_types'       => true,
            'combine_consecutive_issets' => true,
            'combine_consecutive_unsets' => true,
            'psr_autoloading'            => true,
            'phpdoc_to_param_type'       => [
                'scalar_types' => true,
                'union_types'  => true,
            ],
            'phpdoc_to_return_type'      => [
                'scalar_types' => true,
                'union_types'  => true,
            ],
            'return_type_declaration'    => [
                'space_before' => 'one',
            ],
            'braces_position'            => [
                'functions_opening_brace' => 'same_line',
            ],
        ]
    )
    ->setFinder($finder);