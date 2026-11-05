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

namespace Rekalogika\DoctrineAdvancedGroupBy\Walker;

use Doctrine\ORM\Query\AST\GroupByClause;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\SqlOutputWalker;
use Rekalogika\DoctrineAdvancedGroupBy\Cube;
use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\FieldSet;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\GroupingSet;
use Rekalogika\DoctrineAdvancedGroupBy\Item;
use Rekalogika\DoctrineAdvancedGroupBy\RollUp;

final class CustomGroupBySqlWalker extends SqlOutputWalker
{
    public const GROUP_BY = self::class . '::GROUP_BY';

    /**
     * Placeholder clause installed on the top-level SELECT statement so that
     * walkGroupByClause() is invoked. Only this instance is replaced by the
     * custom GROUP BY; any other clause (e.g. a GROUP BY inside a subselect)
     * is rendered normally.
     */
    private ?GroupByClause $placeholder = null;

    /**
     * The GROUP BY clause written in DQL, if any. Its items are emitted in
     * front of the custom GROUP BY items instead of being discarded.
     */
    private ?GroupByClause $dqlGroupByClause = null;

    #[\Override]
    protected function createSqlForFinalizer(SelectStatement $selectStatement): string
    {
        $this->dqlGroupByClause = $selectStatement->groupByClause;

        $this->placeholder = new GroupByClause([]);
        $selectStatement->groupByClause = $this->placeholder;

        return parent::createSqlForFinalizer($selectStatement);
    }

    #[\Override]
    public function walkGroupByClause(GroupByClause $groupByClause): string
    {
        if ($groupByClause !== $this->placeholder) {
            return parent::walkGroupByClause($groupByClause);
        }

        $groupBy = $this->getQuery()->getHint(self::GROUP_BY);

        if (!$groupBy instanceof GroupBy) {
            throw new \RuntimeException('CustomGroupBySqlWalker requires a GroupBy hint');
        }

        return $this->walkCustomGroupByClause($groupBy, $this->dqlGroupByClause);
    }

    public function walkCustomGroupByClause(
        GroupBy $groupBy,
        ?GroupByClause $dqlGroupByClause = null,
    ): string {
        $sqlParts = [];

        foreach ($dqlGroupByClause->groupByItems ?? [] as $groupByItem) {
            if (!\is_string($groupByItem) && !$groupByItem instanceof PathExpression) {
                throw new \RuntimeException('Unsupported DQL GROUP BY item');
            }

            $sqlParts[] = $this->walkGroupByItem($groupByItem);
        }

        foreach ($groupBy as $item) {
            $sqlParts[] = $this->walkCustomGroupByItem($item);
        }

        return ' GROUP BY DISTINCT ' . implode(', ', $sqlParts);
    }

    private function walkCustomGroupByItem(Item $item): string
    {
        if ($item instanceof Field) {
            return $this->walkCustomGroupByField($item);
        } elseif ($item instanceof Cube) {
            return $this->walkCustomGroupByCube($item);
        } elseif ($item instanceof RollUp) {
            return $this->walkCustomGroupByRollUp($item);
        } elseif ($item instanceof FieldSet) {
            return $this->walkCustomGroupByFieldset($item);
        } elseif ($item instanceof GroupingSet) {
            return $this->walkCustomGroupByGroupingSet($item);
        }

        throw new \RuntimeException('Unknown group by item');
    }

    private function walkCustomGroupByFieldset(FieldSet $fieldSet): string
    {
        return '('
            . implode(
                ', ',
                array_map(
                    $this->walkCustomGroupByField(...),
                    iterator_to_array($fieldSet),
                ),
            )
            . ')';
    }

    private function walkCustomGroupByField(Field $field): string
    {
        return $this->walkGroupByItem($field->getContent());
    }

    private function walkCustomGroupByCube(Cube $cube): string
    {
        return
            'CUBE('
            . implode(
                ', ',
                array_map(
                    $this->walkCustomGroupByItem(...),
                    iterator_to_array($cube),
                ),
            )
            . ')';
    }

    private function walkCustomGroupByRollUp(RollUp $rollUp): string
    {
        return
            'ROLLUP('
            . implode(
                ', ',
                array_map(
                    $this->walkCustomGroupByItem(...),
                    iterator_to_array($rollUp),
                ),
            )
            . ')';
    }

    private function walkCustomGroupByGroupingSet(GroupingSet $groupingSet): string
    {
        return
            'GROUPING SETS('
            . implode(
                ', ',
                array_map(
                    $this->walkCustomGroupByItem(...),
                    iterator_to_array($groupingSet),
                ),
            )
            . ')';
    }
}
