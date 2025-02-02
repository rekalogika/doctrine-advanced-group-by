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

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

final class EntityManagerFactory
{
    public function getEntityManager(): EntityManagerInterface
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '../Entity'],
            isDevMode: true,
        );

        $platform = PlatformFactory::getPlatform();

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'port' => 5432,
            'dbname' => 'app',
            'user' => 'app',
            'password' => 'app',
            'serverVersion' => '17',
        ], $config);

        return new EntityManager($connection, $config);
    }
}
