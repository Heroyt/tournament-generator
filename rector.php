<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    )
    ->withPhpSets(php84: true)
    ->withAttributesSets(
        phpunit: true,
    )
    ->withPreparedSets(
        deadCode        : true,
        codeQuality     : true,
        typeDeclarations: true,
        privatization   : true,
        naming          : true,
    )
    ->withComposerBased(
        phpunit   : true,
        netteUtils: true,
    )
;
