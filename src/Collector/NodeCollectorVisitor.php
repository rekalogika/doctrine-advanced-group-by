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

namespace Rekalogika\DoctrineAdvancedGroupBy\Collector;

use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;
use Rekalogika\DoctrineAdvancedGroupBy\Visitor\AbstractVisitor;

final class NodeCollectorVisitor extends AbstractVisitor
{
    public function __construct(private readonly NodeCollector $collector) {}

    #[\Override]
    public function visitGroupBy(GroupBy $groupBy): void
    {
        $this->collector->add($groupBy, null);

        foreach ($groupBy as $item) {
            $this->collector->add($item, $groupBy);
            $item->accept($this);
        }
    }

    #[\Override]
    public function visitFieldSet(FieldSet $fieldSet): void
    {
        foreach ($fieldSet as $item) {
            $this->collector->add($item, $fieldSet);
            $item->accept($this);
        }
    }

    #[\Override]
    public function visitField(Field $field): void {}

    #[\Override]
    public function visitCube(Cube $cube): void
    {
        foreach ($cube as $item) {
            $this->collector->add($item, $cube);
            $item->accept($this);
        }
    }

    #[\Override]
    public function visitRollUp(RollUp $rollUp): void
    {
        foreach ($rollUp as $item) {
            $this->collector->add($item, $rollUp);
            $item->accept($this);
        }
    }

    #[\Override]
    public function visitGroupingSets(GroupingSet $groupingSet): void
    {
        foreach ($groupingSet as $item) {
            $this->collector->add($item, $groupingSet);
            $item->accept($this);
        }
    }
}
