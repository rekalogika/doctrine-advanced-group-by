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

use Rekalogika\DoctrineAdvancedGroupBy\Field;
use Rekalogika\DoctrineAdvancedGroupBy\GroupBy;
use Rekalogika\DoctrineAdvancedGroupBy\Item;

final class NodeCollector
{
    /**
     * @var array<int,Item>
     */
    private array $item = [];

    /**
     * @var array<int,int>
     */
    private array $itemToParent = [];

    /**
     * @var array<string,list<int>>
     */
    private array $fieldContentToFields = [];

    public function __construct(GroupBy $groupBy)
    {
        $visitor = new NodeCollectorVisitor($this);
        $groupBy->accept($visitor);
    }

    public function add(Item $item, ?Item $parent): void
    {
        $this->item[spl_object_id($item)] = $item;

        if ($parent !== null) {
            $this->itemToParent[spl_object_id($item)] = spl_object_id($parent);
        }

        if ($item instanceof Field) {
            $content = $item->getContent();

            $this->fieldContentToFields[$content][] = spl_object_id($item);
        }
    }

    public function getParent(Item $item): ?Item
    {
        $id = spl_object_id($item);

        if (!isset($this->itemToParent[$id])) {
            return null;
        }

        $parentId = $this->itemToParent[$id];

        return $this->item[$parentId] ?? null;
    }

    /**
     * @return list<Field>
     */
    public function getFieldsByContent(string $content): array
    {
        $ids = $this->fieldContentToFields[$content] ?? [];
        $fields = [];

        foreach ($ids as $id) {
            if (!isset($this->item[$id])) {
                throw new \RuntimeException(\sprintf('Field with content "%s" not found.', $content));
            }

            if (!$this->item[$id] instanceof Field) {
                throw new \RuntimeException(\sprintf('Item with content "%s" is not a Field.', $content));
            }

            $fields[] = $this->item[$id];
        }

        return $fields;
    }
}
