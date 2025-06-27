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

namespace Rekalogika\DoctrineAdvancedGroupBy\SqlRenderer;

use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;
use Rekalogika\DoctrineAdvancedGroupBy\Visitor\Visitor;

/**
 * @implements Visitor<string>
 */
final class SqlRenderer implements Visitor
{
    public function getSql(GroupBy $groupBy): string
    {
        return $groupBy->accept($this);
    }

    #[\Override]
    public function visitGroupBy(GroupBy $groupBy): mixed
    {
        $sql = 'GROUP BY ';

        foreach ($groupBy as $item) {
            $sql .= $item->accept($this) . ', ';
        }

        return rtrim($sql, ', ');
    }

    #[\Override]
    public function visitFieldSet(FieldSet $fieldSet): mixed
    {
        $sql = '(';

        foreach ($fieldSet as $item) {
            $sql .= $item->accept($this) . ', ';
        }

        return rtrim($sql, ', ') . ')';
    }

    #[\Override]
    public function visitField(Field $field): mixed
    {
        return $field->getContent();
    }

    #[\Override]
    public function visitCube(Cube $cube): mixed
    {
        $sql = 'CUBE (';

        foreach ($cube as $item) {
            $sql .= $item->accept($this) . ', ';
        }

        return rtrim($sql, ', ') . ')';
    }

    #[\Override]
    public function visitRollUp(RollUp $rollUp): mixed
    {
        $sql = 'ROLLUP (';

        foreach ($rollUp as $item) {
            $sql .= $item->accept($this) . ', ';
        }

        return rtrim($sql, ', ') . ')';
    }

    #[\Override]
    public function visitGroupingSets(GroupingSet $groupingSet): mixed
    {
        $sql = 'GROUPING SETS (';

        foreach ($groupingSet as $item) {
            $sql .= $item->accept($this) . ', ';
        }

        return rtrim($sql, ', ') . ')';
    }
}
