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

use PHPUnit\Framework\TestCase;
use Rekalogika\DoctrineAdvancedGroupBy\Collector\NodeCollector;
use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;

final class NodeCollectorTest extends TestCase
{
    public function testNodeCollector(): void
    {
        $groupBy = new GroupBy(
            new GroupingSet(
                new Cube(
                    new Field('a'),
                ),
                new Cube(
                    new Field('b'),
                ),
                new GroupingSet(
                    new RollUp(
                        new Field('c'),
                        new FieldSet(
                            new Field('d'),
                            new Field('e'),
                        ),
                        new FieldSet(
                            new Field('f'),
                            new Field('g'),
                        ),
                    ),
                ),
            ),
        );

        $nodeCollector = new NodeCollector($groupBy);
        $ds = $nodeCollector->getFieldsByContent('d');

        $this->assertCount(1, $ds);
        $d = $ds[0];

        $parent = $nodeCollector->getParent($d);
        $this->assertInstanceOf(FieldSet::class, $parent);
        $this->assertCount(2, $parent);

        $parent = $nodeCollector->getParent($parent);
        $this->assertInstanceOf(RollUp::class, $parent);
        $this->assertCount(3, $parent);

        $parent = $nodeCollector->getParent($parent);
        $this->assertInstanceOf(GroupingSet::class, $parent);
        $this->assertCount(1, $parent);

        $parent = $nodeCollector->getParent($parent);
        $this->assertInstanceOf(GroupingSet::class, $parent);
        $this->assertCount(3, $parent);
    }
}
