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

final readonly class Field implements Item
{
    public function __construct(private string $content) {}

    #[\Override]
    public function accept(Visitor $visitor): mixed
    {
        return $visitor->visitField($this);
    }

    #[\Override]
    public function count(): int
    {
        return 1;
    }

    #[\Override]
    public function getSignature(): string
    {
        return hash('xxh128', self::class . $this->content);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @deprecated Use getContent() instead.
     */
    public function getName(): string
    {
        return $this->content;
    }
}
