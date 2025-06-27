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

namespace Rekalogika\DoctrineAdvancedGroupBy;

use Rekalogika\DoctrineAdvancedGroupBy\Visitor\Visitor;

interface Item extends \Countable
{
    public function getSignature(): string;

    /**
     * @template T
     * @param Visitor<T> $visitor
     * @return T
     */
    public function accept(Visitor $visitor): mixed;
}
