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
 * @template T
 */
interface Visitor
{
    /**
     * @return T
     */
    public function visitGroupBy(GroupBy $groupBy): mixed;

    /**
     * @return T
     */
    public function visitFieldSet(FieldSet $fieldSet): mixed;

    /**
     * @return T
     */
    public function visitField(Field $field): mixed;

    /**
     * @return T
     */
    public function visitCube(Cube $cube): mixed;

    /**
     * @return T
     */
    public function visitRollUp(RollUp $rollUp): mixed;

    /**
     * @return T
     */
    public function visitGroupingSets(GroupingSet $groupingSet): mixed;
}
