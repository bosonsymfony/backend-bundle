<?php

namespace UCI\Boson\BackendBundle\Doctrine\ORM\Query\AST\Platform\Functions\Mysql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use UCI\Boson\BackendBundle\Doctrine\ORM\Query\AST\Functions\SimpleFunction;
use UCI\Boson\BackendBundle\Doctrine\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class Dayofyear extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $expression */
        $expression = $this->parameters[SimpleFunction::PARAMETER_KEY];
        return 'DAYOFYEAR(' . $this->getExpressionValue($expression, $sqlWalker) . ')';
    }
}
