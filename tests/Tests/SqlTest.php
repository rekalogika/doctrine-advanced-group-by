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
use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;
use Rekalogika\DoctrineAdvancedGroupBy\SqlRenderer\SqlRenderer;

final class SqlTest extends TestCase
{
    /**
     * @dataProvider sqlProvider
     */
    public function testSqlRendering(GroupBy $groupBy, string $expected): void
    {
        $sqlRenderer = new SqlRenderer();
        $sql = $sqlRenderer->getSql($groupBy);

        $this->assertSame($expected, $sql);
    }

    /**
     * @return iterable<array-key,array{GroupBy,string}>
     */
    public static function sqlProvider(): iterable
    {
        yield 'simple group by' => [
            new GroupBy(new Field('a'), new Field('b')),
            'GROUP BY a, b',
        ];

        yield 'group by with field set' => [
            new GroupBy(
                new Field('a'),
                new FieldSet(new Field('b'), new Field('c')),
            ),
            'GROUP BY a, (b, c)',
        ];

        yield 'group by with grouping sets' => [
            new GroupBy(
                new GroupingSet(
                    new FieldSet(new Field('a'), new Field('b')),
                    new FieldSet(new Field('c'), new Field('d')),
                ),
            ),
            'GROUP BY GROUPING SETS ((a, b), (c, d))',
        ];

        yield 'group by with rollup' => [
            new GroupBy(
                new RollUp(new Field('c'), new Field('d'), new Field('e')),
            ),
            'GROUP BY ROLLUP (c, d, e)',
        ];

        yield 'group by with cube' => [
            new GroupBy(
                new Cube(new Field('f'), new Field('g'), new Field('h')),
            ),
            'GROUP BY CUBE (f, g, h)',
        ];
    }
}
