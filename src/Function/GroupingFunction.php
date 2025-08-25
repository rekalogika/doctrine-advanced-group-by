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

namespace Rekalogika\DoctrineAdvancedGroupBy\Function;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * "GROUPING" "(" GroupByExpression {"," GroupByExpression}* ")"
 *
 * DQL User Defined Function for the SQL GROUPING function.
 *
 * The GROUPING function returns a bit mask indicating which GROUP BY expressions
 * are not included in the current grouping set. It is used with GROUPING SETS,
 * ROLLUP, and CUBE operations to distinguish result rows.
 *
 * Example usage in DQL:
 * SELECT e.category, e.product, GROUPING(e.category) as grp_cat,
 *        GROUPING(e.product) as grp_prod, SUM(e.sales)
 * FROM SalesEntity e
 * GROUP BY ROLLUP(e.category, e.product)
 */
final class GroupingFunction extends FunctionNode
{
    /**
     * @var list<Node|string>
     */
    public array $expressions = [];

    #[\Override]
    public function getSql(SqlWalker $sqlWalker): string
    {
        $args = [];

        foreach ($this->expressions as $expression) {
            $args[] = $sqlWalker->walkSimpleArithmeticExpression($expression);
        }

        return 'GROUPING(' . implode(', ', $args) . ')';
    }

    #[\Override]
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); // GROUPING
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        // Parse the first expression
        $this->expressions[] = $parser->SimpleArithmeticExpression();

        // Parse additional expressions separated by commas
        while ($parser->getLexer()->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->expressions[] = $parser->SimpleArithmeticExpression();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
