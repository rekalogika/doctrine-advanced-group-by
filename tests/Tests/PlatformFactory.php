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

namespace Rekalogika\DoctrineAdvancedGroupBy\Tests\Tests;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

final class PlatformFactory
{
    public static function getPlatform(): AbstractPlatform
    {
        if (class_exists(PostgreSQLPlatform::class)) {
            return new PostgreSQLPlatform();
        } elseif (class_exists(PostgreSQL100Platform::class)) {
            return new PostgreSQL100Platform();
        } else {
            throw new \RuntimeException('No PostgreSQLPlatform found');
        }
    }
}
