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

namespace Rekalogika\DoctrineAdvancedGroupBy\Visitor;

use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;

/**
 * @implements Visitor<null>
 */
abstract class AbstractVisitor implements Visitor
{
    #[\Override]
    public function visitGroupBy(GroupBy $groupBy): mixed
    {
        foreach ($groupBy as $item) {
            $item->accept($this);
        }

        return null;
    }

    #[\Override]
    public function visitFieldSet(FieldSet $fieldSet): mixed
    {
        foreach ($fieldSet as $item) {
            $item->accept($this);
        }

        return null;
    }

    #[\Override]
    public function visitField(Field $field): mixed
    {
        return null;
    }

    #[\Override]
    public function visitCube(Cube $cube): mixed
    {
        foreach ($cube as $item) {
            $item->accept($this);
        }

        return null;
    }

    #[\Override]
    public function visitRollUp(RollUp $rollUp): mixed
    {
        foreach ($rollUp as $item) {
            $item->accept($this);
        }

        return null;
    }

    #[\Override]
    public function visitGroupingSets(GroupingSet $groupingSet): mixed
    {
        foreach ($groupingSet as $item) {
            $item->accept($this);
        }

        return null;
    }
}
