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

namespace Rekalogika\DoctrineAdvancedGroupBy\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "some_entity")]
class SomeEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $a = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $b = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $c = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $d = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $e = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $f = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $g = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $h = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $i = null;

    #[ORM\Column(type: "string", length: 255)]
    public ?string $j = null;
}
