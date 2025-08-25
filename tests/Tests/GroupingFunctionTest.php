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

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Rekalogika\DoctrineAdvancedGroupBy\Function\GroupingFunction;
use Rekalogika\DoctrineAdvancedGroupBy\Tests\Entity\SomeEntity;

final class GroupingFunctionTest extends TestCase
{
    private ?EntityManagerInterface $entityManager = null;

    private function getEntityManager(): EntityManagerInterface
    {
        if ($this->entityManager === null) {
            $this->entityManager = (new EntityManagerFactory())->getEntityManager();

            // Register the GROUPING function
            $config = $this->entityManager->getConfiguration();
            $config->addCustomNumericFunction('GROUPING', GroupingFunction::class);
        }

        return $this->entityManager;
    }

    public function testGroupingFunction(): void
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->from(SomeEntity::class, 'e')
            ->select('e.a AS a')
            ->addSelect('GROUPING(e.a)')
            ->addSelect('GROUPING(e.a, e.b)');

        $query = $queryBuilder->getQuery();
        $sql = $query->getSQL();
        $sqlString = \is_array($sql) ? implode(' ', $sql) : $sql;

        $this->assertEquals('SELECT s0_.a AS a_0, GROUPING(s0_.a) AS sclr_1, GROUPING(s0_.a, s0_.b) AS sclr_2 FROM some_entity s0_', $sqlString);
    }
}
