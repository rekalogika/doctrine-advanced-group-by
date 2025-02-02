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
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\Tests\Entity\SomeEntity;

class GroupByTest extends TestCase
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

        $groupBy = (new GroupBy())
            ->add(new Field('a'))
            ->add(new Field('b'));

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

        $groupBy = (new GroupBy())
            ->add(
                (new GroupingSet())
                    ->add(
                        (new FieldSet())
                            ->add(new Field('a'))
                            ->add(new Field('b')),
                    )
                    ->add(
                        (new FieldSet())
                            ->add(new Field('c'))
                            ->add(new Field('d')),
                    ),
            );

        $query = $queryBuilder->getQuery();
        $groupBy->apply($query);

        $this->assertSame(
            'SELECT s0_.a AS a_0, s0_.b AS b_1, s0_.c AS c_2, s0_.d AS d_3 FROM some_entity s0_ GROUP BY DISTINCT GROUPING SETS((s0_.a, s0_.b), (s0_.c, s0_.d))',
            $query->getSQL(),
        );
    }
}
