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
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;
use Rekalogika\DoctrineAdvancedGroupBy\Tests\Entity\SomeEntity;

final class GroupByTest extends TestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager ??= (new EntityManagerFactory())->getEntityManager();
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    public function testWiring(): void
    {
        $metadata = $this->getEntityManager()->getClassMetadata(SomeEntity::class);
        $this->assertSame(SomeEntity::class, $metadata->getName());
        $this->assertSame('some_entity', $metadata->getTableName());
    }

    public function testField(): void
    {
        $queryBuilder = $this->createQueryBuilder()
            ->from(SomeEntity::class, 'e')
            ->select('e.a AS a')
            ->addSelect('e.b AS b');

        $groupBy = new GroupBy(
            new Field('a'),
            new Field('b'),
        );

        $query = $queryBuilder->getQuery();
        $groupBy->apply($query);

        $this->assertSame(
            'SELECT s0_.a AS a_0, s0_.b AS b_1 FROM some_entity s0_ GROUP BY DISTINCT s0_.a, s0_.b',
            $query->getSQL(),
        );
    }

    public function testGroupingSet(): void
    {
        $queryBuilder = $this->createQueryBuilder()
            ->from(SomeEntity::class, 'e')
            ->select('e.a AS a')
            ->addSelect('e.b AS b')
            ->addSelect('e.c AS c')
            ->addSelect('e.d AS d');

        $groupBy = new GroupBy(
            new GroupingSet(
                new FieldSet(
                    new Field('a'),
                    new Field('b'),
                ),
                new FieldSet(
                    new Field('c'),
                    new Field('d'),
                ),
            ),
        );

        $query = $queryBuilder->getQuery();
        $groupBy->apply($query);

        $this->assertSame(
            'SELECT s0_.a AS a_0, s0_.b AS b_1, s0_.c AS c_2, s0_.d AS d_3 FROM some_entity s0_ GROUP BY DISTINCT GROUPING SETS((s0_.a, s0_.b), (s0_.c, s0_.d))',
            $query->getSQL(),
        );
    }

    public function testFieldGroupingSetFlattening(): void
    {
        $groupBy1 = new GroupBy(
            new Field('a'),
            new GroupingSet(
                new FieldSet(),
                new FieldSet(
                    new Field('b'),
                ),
                new FieldSet(
                    new Field('c'),
                    new Field('d'),
                ),
            ),
        );

        $groupBy2 = new GroupBy(
            new GroupingSet(
                new FieldSet(
                    new Field('a'),
                ),
                new FieldSet(
                    new Field('a'),
                    new Field('b'),
                ),
                new FieldSet(
                    new Field('a'),
                    new Field('c'),
                    new Field('d'),
                ),
            ),
        );

        $this->assertEqualsCanonicalizing($groupBy2, $groupBy1->flatten());
    }

    public function testCubeFlattening(): void
    {
        $groupBy1 = new GroupBy(
            new Cube(
                new Field('a'),
                new Field('b'),
            ),
        );

        $groupBy2 = new GroupBy(
            new GroupingSet(
                new FieldSet(),
                new FieldSet(
                    new Field('a'),
                ),
                new FieldSet(
                    new Field('b'),
                ),
                new FieldSet(
                    new Field('a'),
                    new Field('b'),
                ),
            ),
        );

        $this->assertEqualsCanonicalizing($groupBy2, $groupBy1->flatten());
    }

    public function testRollupFlattening(): void
    {
        $groupBy1 = new GroupBy(
            new RollUp(
                new Field('a'),
                new Field('b'),
            ),
        );

        $groupBy2 = new GroupBy(
            new GroupingSet(
                new FieldSet(),
                new FieldSet(
                    new Field('a'),
                ),
                new FieldSet(
                    new Field('a'),
                    new Field('b'),
                ),
            ),
        );

        $this->assertEqualsCanonicalizing($groupBy2, $groupBy1->flatten());
    }

    public function testLimit(): void
    {
        $queryBuilder = $this->createQueryBuilder()
            ->from(SomeEntity::class, 'e')
            ->select('e.a AS a');

        $groupBy = new GroupBy(
            new Field('a'),
        );

        $query = $queryBuilder->getQuery();
        $groupBy->apply($query);
        $query->setFirstResult(5);
        $query->setMaxResults(10);

        $this->assertSame(
            'SELECT s0_.a AS a_0 FROM some_entity s0_ GROUP BY DISTINCT s0_.a LIMIT 10 OFFSET 5',
            $query->getSQL(),
        );
    }
}
