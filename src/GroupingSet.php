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

/**
 * @implements \IteratorAggregate<FieldSet|RollUp|Cube|GroupingSet>
 */
final class GroupingSet implements Item, \IteratorAggregate
{
    /**
     * @var list<FieldSet|RollUp|Cube|GroupingSet>
     */
    private array $items = [];

    public function __construct(FieldSet|RollUp|Cube|GroupingSet ...$fields)
    {
        $this->items = array_values($fields);
    }

    #[\Override]
    public function accept(Visitor $visitor): void
    {
        $visitor->visitGroupingSets($this);
    }

    #[\Override]
    public function count(): int
    {
        return \count($this->items);
    }

    #[\Override]
    public function getSignature(): string
    {
        return hash(
            'xxh128',
            self::class . implode(
                '',
                array_map(
                    fn(FieldSet|RollUp|Cube|GroupingSet $item): string => $item->getSignature(),
                    $this->items,
                ),
            ),
        );
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function add(FieldSet|RollUp|Cube|GroupingSet $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function flatten(): GroupingSet
    {
        $fieldSets = [];

        foreach ($this->items as $item) {
            if ($item instanceof RollUp || $item instanceof Cube) {
                $item = $item->flatten();
            } else { // instanceof FieldSet
                $item = new self($item);
            }

            foreach ($item as $fieldSet) {
                if (!$fieldSet instanceof FieldSet) {
                    throw new \RuntimeException('Expected FieldSet');
                }

                $fieldSets[$fieldSet->getSignature()] = $fieldSet;
            }
        }

        $groupingSet = new GroupingSet();

        foreach ($fieldSets as $fieldSet) {
            $groupingSet->add($fieldSet);
        }

        return $groupingSet;
    }
}
