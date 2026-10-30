<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/doctrine-advanced-group-by package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withImportNames(importShortClasses: false)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        symfonyCodeQuality: true,
        doctrineCodeQuality: true,
    )
    ->withPhpSets(php82: true)
    ->withSkip([
        // results in too long variables
        CatchExceptionNameMatchingTypeRector::class,
    ]);
